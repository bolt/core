<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Date;
use Bolt\Common\Json;
use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\CollectionField;
use Bolt\Entity\Field\SetField;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\Relation;
use Bolt\Entity\User;
use Bolt\Enum\Statuses;
use Bolt\Event\ContentEvent;
use Bolt\Event\Listener\ContentFillListener;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\Repository\MediaRepository;
use Bolt\Repository\RelationRepository;
use Bolt\Repository\TaxonomyRepository;
use Bolt\Security\ContentVoter;
use Bolt\Utils\ContentHelper;
use Bolt\Utils\TranslationsManager;
use Bolt\Validator\ContentValidatorInterface;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tightenco\Collect\Support\Collection;

/**
 * CRUD + status, duplicate, for content - note that listing is handled by ListingController.php
 */
class ContentEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /** @var RelationRepository */
    private $relationRepository;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ContentFillListener */
    private $contentFillListener;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var string */
    protected $defaultLocale;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentHelper */
    private $contentHelper;

    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        RelationRepository $relationRepository,
        ContentRepository $contentRepository,
        MediaRepository $mediaRepository,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ContentFillListener $contentFillListener,
        EventDispatcherInterface $dispatcher,
        string $defaultLocale,
        TranslatorInterface $translator,
        ContentHelper $contentHelper
    ) {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->relationRepository = $relationRepository;
        $this->contentRepository = $contentRepository;
        $this->mediaRepository = $mediaRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->contentFillListener = $contentFillListener;
        $this->dispatcher = $dispatcher;
        $this->defaultLocale = $defaultLocale;
        $this->translator = $translator;
        $this->contentHelper = $contentHelper;
    }

    /**
     * @Route("/new/{contentType}", name="bolt_content_new", methods={"GET|POST"})
     */
    public function new(string $contentType, ?ContentValidatorInterface $contentValidator = null): Response
    {
        $content = new Content();

        /** @var User $user */
        $user = $this->getUser();

        $content->setAuthor($user);
        $content->setContentType($contentType);

        // content now has a contentType -> permission check possible
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CREATE, $content);

        $this->contentFillListener->fillContent($content);

        if ($this->request->getMethod() === 'POST') {
            $content->setPublishedAt(null);
            $content->setDepublishedAt(null);

            return $this->save($content, $contentValidator);
        }

        return $this->edit($content);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function edit(Content $content): Response
    {
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_EDIT, $content);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::ON_EDIT);

        return $this->renderEditor($content);
    }


    /**
     * @Route("/edit/{id}", name="bolt_content_edit_post", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function save(?Content $originalContent = null, ?ContentValidatorInterface $contentValidator = null): Response
    {
        $this->validateCsrf('editrecord');

        // pre-check on original content, store properties for later comparison
        if ($originalContent !== null) {
            $this->denyAccessUnlessGranted(ContentVoter::CONTENT_EDIT, $originalContent);
            $originalStatus = $originalContent->getStatus();
            $originalPublishedAt = $originalContent->getPublishedAt();
            $originalDepublishedAt = $originalContent->getDepublishedAt();
            $originalAuthor = $originalContent->getAuthor();
        } else {
            $originalStatus = null;
            $originalPublishedAt = null;
            $originalDepublishedAt = null;
            $originalAuthor = null;
        }

        $content = $this->contentFromPost($originalContent);

        // check again on new/updated content, this is needed in case the save action is used to create a new item
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_EDIT, $content);

        // check for status changes
        if ($originalContent !== null) {
            // deny if we detect the status field being changed
            if ($originalStatus !== $content->getStatus() ) {
                $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CHANGE_STATUS, $content);
            }

            // deny if we detect the publication dates field being changed
            if (($originalPublishedAt !== null && Date::datesDiffer($originalPublishedAt, $content->getPublishedAt())) ||
                ($originalDepublishedAt !== null && Date::datesDiffer($originalDepublishedAt, $content->getDepublishedAt()))
            ) {
                $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CHANGE_STATUS, $content);
            }

            // deny if owner changes
            if ($originalAuthor !== $content->getAuthor()) {
                $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CHANGE_OWNERSHIP, $content);
            }
        }

        // check if validator should be enabled (default for bolt 4.x is not enabled)
        $enableContentValidator = $this->config->get('general/validator_options/enable', false);
        if ($enableContentValidator && $contentValidator) {
            $constraintViolations = $contentValidator->validate($content);
            if (count($constraintViolations) > 0) {
                $this->addFlash('danger', 'content.validation_errors');

                return $this->renderEditor($content, $constraintViolations);
            }
        }

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_SAVE);

        /* Note: Doctrine also calls preUpdate() -> Event/Listener/FieldFillListener.php */
        $this->em->persist($content);
        $this->em->flush();

        // Set the list_format of the Record in the bolt_content table. This has to be done in a 2nd iteration because
        // $content does not have id set untill the Entity Manager is flushed.
        $content->setListFormat();
        $this->em->persist($content);
        $this->em->flush();

        $urlParams = [
            'id' => $content->getId(),
            'edit_locale' => $this->getEditLocale($content) ?: null,
        ];
        $url = $this->urlGenerator->generate('bolt_content_edit', $urlParams);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::POST_SAVE);

        $locale = $originalAuthor->getLocale();

        // If we're "Saving Ajaxy"
        if ($this->request->isXmlHttpRequest()) {
            $modified = sprintf(
                '(%s: %s)',
                $this->translator->trans('field.modifiedAt', [], null, $locale),
                $this->contentHelper->get($content, "{modifiedAt}")
            );

            return new JsonResponse([
                'url' => $url,
                'status' => 'success',
                'type' => $this->translator->trans('success', [], null, $locale),
                'message' => $this->translator->trans('content.updated_successfully', [], null, $locale),
                'notification' => $this->translator->trans('flash_messages.notification', [], null, $locale),
                'title' => $content->getExtras()['title'],
                'modified' => $modified,
            ], 200
            );
        }

        // Otherwise, treat it as a normal POST-request cycle..
        $this->addFlash('success', 'content.updated_successfully');

        return new RedirectResponse($url);
    }

    /**
     * @Route("/duplicate/{id}", name="bolt_content_duplicate", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function duplicate(Content $content): Response
    {
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CREATE, $content);

        /** @var User $user */
        $user = $this->getUser();

        $content->setId(0);
        $content->setCreatedAt(null);
        $content->setAuthor($user);
        $content->setModifiedAt(null);
        $content->setDepublishedAt(null);
        $content->setPublishedAt(null);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::ON_DUPLICATE);

        $twigvars = [
            'record' => $content,
            'locales' => $content->getLocales(),
            'currentlocale' => $this->getEditLocale($content),
            'defaultlocale' => $this->defaultLocale,
        ];

        return $this->render('@bolt/content/edit.html.twig', $twigvars);
    }

    /**
     * @Route("/duplicate/{id}", name="bolt_content_duplicate_post", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function duplicateSave(?Content $content = null): Response
    {
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CREATE, $content);

        return $this->new($content->getContentType());
    }

    /**
     * @Route("/status/{id}", name="bolt_content_status", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function status(Content $content): Response
    {
        $this->validateCsrf('status');

        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CHANGE_STATUS, $content);

        $content->setStatus($this->getFromRequest('status'));

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_STATUS_CHANGE);

        $this->em->persist($content);
        $this->em->flush();

        $this->addFlash('success', 'content.status_changed_successfully');

        $params = ['contentType' => $content->getContentTypeSlug()];
        $url = $this->urlGenerator->generate('bolt_content_overview', $params);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::POST_STATUS_CHANGE);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/delete/{id}", name="bolt_content_delete", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function delete(Content $content): Response
    {
        $this->validateCsrf('delete');

        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_DELETE, $content);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_DELETE);

        $this->em->remove($content);
        $this->em->flush();

        $this->addFlash('success', 'content.deleted_successfully');

        $params = ['contentType' => $content->getContentTypeSlug()];
        $url = $this->urlGenerator->generate('bolt_content_overview', $params);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::POST_DELETE);

        return new RedirectResponse($url);
    }

    // todo: This function should not be public.
    // It needs to be abstracted into its own class, alongside the other functions it uses.
    public function contentFromPost(?Content $content): Content
    {
        $formData = $this->request->request->all();
        $locale = $this->getPostedLocale($formData) ?: $content->getDefaultLocale();

        /** @var User $user */
        $user = $this->getUser();

        if ($content === null) {
            $content = new Content();
            $content->setAuthor($user);
            $content->setContentType($this->request->attributes->get('id'));
        }
        $this->contentFillListener->fillContent($content);

        $content->setPublishedAt(! empty($formData['publishedAt']) ? new Carbon($formData['publishedAt']) : null);
        $content->setDepublishedAt(! empty($formData['depublishedAt']) ? new Carbon($formData['depublishedAt']) : null);

        $status = Json::findScalar($formData['status']);
        if (in_array($status, Statuses::all(), true) === true) {
            $content->setStatus($status);
        }

        if (isset($formData['fields'])) {
            foreach ($formData['fields'] as $fieldName => $fieldValue) {
                $field = $this->getFieldToUpdate($content, $fieldName);
                $this->updateField($field, $fieldValue, $locale);
            }
        }

        if (isset($formData['sets'])) {
            foreach ($formData['sets'] as $setName => $setItems) {
                $setDefinition = $content->getDefinition()->get('fields')->get($setName);
                $set = $this->getFieldToUpdate($content, $setName, $setDefinition);
                $this->updateField($set, $setItems, $locale);
            }
        }

        $this->updateCollections($content, $formData, $locale);

        if (isset($formData['taxonomy'])) {
            foreach ($formData['taxonomy'] as $fieldName => $taxonomy) {
                if (false !== mb_strpos($fieldName, 'sortorder')) {
                    continue;
                }
                $order = 0;
                if (isset($formData['taxonomy'][$fieldName . '-sortorder'])) {
                    $order = intval($formData['taxonomy'][$fieldName . '-sortorder']);
                }
                $this->updateTaxonomy($content, $fieldName, $taxonomy, $order);
            }
        }

        if (isset($formData['relationship'])) {
            foreach ($formData['relationship'] as $relationType => $relation) {
                $this->updateRelation($content, $relationType, $relation);
            }
        }

        return $content;
    }

    private function removeFieldChildren(Content $content, FieldParentInterface $field): void
    {
        foreach ($field->getValue() as $child) {
            if ($child instanceof FieldParentInterface && ! empty($child->getValue())) {
                $this->removeFieldChildren($content, $child);
            }

            /** @var Field $child */
            $content->removeField($child);

            // Only attempt removal if the entity is already persisted (managed)
            // by the entity manager
            if ($this->em->contains($child)) {
                $this->em->remove($child);
            }
        }
    }

    public function updateCollections(Content $content, array $formData, ?string $locale): void
    {
        $collections = $content->getFields()->filter(function (Field $field) {
            return $field->getType() === CollectionField::TYPE;
        });

        $keys = $formData['keys-collections'] ?? [];
        $tm = new TranslationsManager($collections, $keys);

        foreach ($collections as $collection) {
            $this->removeFieldChildren($content, $collection);
        }

        if (isset($formData['collections'])) {
            foreach ($formData['collections'] as $collectionName => $collectionItems) {
                $collectionDefinition = $content->getDefinition()->get('fields')->get($collectionName);
                $orderArray = array_flip($collectionItems['order']);

                $collection = $this->getFieldToUpdate($content, $collectionName, $collectionDefinition);

                $newFields = [];
                foreach ($collectionItems as $name => $instances) {
                    // order field is only used to determine the order in which fields are submitted
                    if ($name === 'order') {
                        continue;
                    }

                    foreach ($instances as $orderId => $value) {
                        $order = $orderArray[$orderId];
                        $fieldDefinition = $collection->getDefinition()->get('fields')->get($name);
                        $field = FieldRepository::factory($fieldDefinition, $name);
                        // Note, $collection side is set by $collection->setValue() below
                        $field->setParent($collection);
                        $newFields[$order] = $field;
                        $field->setSortorder($order);
                        $content->addField($field);
                        $this->updateField($field, $value, $locale);
                        $tm->applyTranslations($field, $collectionName, $orderId);
                    }
                }
                ksort($newFields);
                $collection->setValue($newFields);
            }
        }
    }

    public function getFieldToUpdate(Content $content, string $fieldName, $fieldDefinition = ''): Field
    {
        /** @var Field $field */
        $field = null;

        $definition = empty($fieldDefinition) ? $content->getDefinition()->get('fields')->get($fieldName) : $fieldDefinition;

        if (empty($definition)) {
            throw new \Exception("Content type `{$content->getContentType()}` doesn't have field `{$fieldName}`.");
        }

        if ($content->hasField($fieldName)) {
            $field = $content->getField($fieldName);
        }

        // If the Field exists, but it has the wrong type, we'll remove the existing one.
        if (($field !== null) && ! $content->hasField($fieldName, true)) {
            $content->removeField($field);
            try {
                $this->em->remove($field);
            } catch (ORMInvalidArgumentException $e) {
                // Suppress "Detached entity Array cannot be removed", because it'd break the Request
            }
            $this->em->flush();
            $field = null;
        }

        // Perhaps create a new Field..
        if (! $field) {
            $field = FieldRepository::factory($definition, $fieldName);

            $field->setName($fieldName);
            $content->addField($field);
        }

        return $field;
    }

    public function updateField(Field $field, $value, ?string $locale): void
    {
        // If the Field is translatable, set the locale
        if ($field->getDefinition()->get('localize')) {
            $field->setLocale($locale);
        } else {
            $field->setLocale($this->defaultLocale);
        }

        if ($field instanceof SetField) {
            $children = [];
            foreach ($value as $name => $svalue) {
                $child = $field->getValueForEditor()[$name] ?? null;

                if (! $child) {
                    // Child has been removed from the definition.
                    continue;
                }

                if (! $child->getId()) {
                    $child->setParent($field);
                    $field->getContent()->addField($child);
                }

                $child->setDefinition($child->getName(), $field->getDefinition()->get('fields')->get($child->getName()));
                $this->updateField($child, $svalue, $locale);
                $children[] = $child;
            }
            $field->setValue($children);
        } else {
            // If the value is an array that contains a string of JSON, parse it
            if (is_iterable($value) && Json::test(current($value))) {
                $value = Json::findArray($value);
            }

            $field->setValue($value);
        }

        // If the Field is MediaAwareInterface, link it to an existing Media Entity
        if ($field instanceof Field\MediaAwareInterface) {
            $field->setLinkedMedia($this->mediaRepository);
        }
    }

    public function updateTaxonomy(Content $content, string $key, $postedTaxonomy, int $order): void
    {
        $postedTaxonomy = (new Collection(Json::findArray($postedTaxonomy)))->filter();
        $contentTaxoSlugs = [];

        // Remove old ones, if they are not in the current ones
        foreach ($content->getTaxonomies($key) as $current) {
            // If it's not still present, remove it
            if (! in_array($current->getSlug(), $postedTaxonomy->all())) {
                $content->removeTaxonomy($current);
            }

            $contentTaxoSlugs[] = $current->getSlug();
        }

        // Then (re-) add selected ones
        foreach ($postedTaxonomy as $slug) {
            // If we already have it, continue.
            if (in_array($slug, $contentTaxoSlugs)) {
                continue;
            }

            $repoTaxonomy = $this->taxonomyRepository->findOneBy([
                'type' => $key,
                'slug' => $slug,
            ]);

            if ($repoTaxonomy === null) {
                $repoTaxonomy = $this->taxonomyRepository->factory($key, (string) $slug);
            }

            $repoTaxonomy->setSortorder($order);

            $content->addTaxonomy($repoTaxonomy);
        }
    }

    private function updateRelation(Content $content, string $relationType, $newRelations): void
    {
        $newRelations = (new Collection(Json::findArray($newRelations)))->filter();
        $currentRelations = new Collection($this->relationRepository->findRelations($content, $relationType, null, false));
        $currentRelationIds = $currentRelations
            ->map(
                static function (Relation $relation) use ($content) {
                    return $relation->getFromContent() === $content
                        ? $relation->getToContent()->getId()
                        : $relation->getFromContent()->getId();
                }
            )
            ->unique();

        // Remove old, no longer used relations.
        foreach ($currentRelations as $currentRelation) {
            if (
                $newRelations->contains($currentRelation->getToContent()->getId())
                || $newRelations->contains($currentRelation->getFromContent()->getId())
            ) {
                // This relation currently exists, and continues to exist.
                continue;
            }

            // unlink content from relation - needed for code using relations from the content
            // side later (e.g. validation)
            if (
                $currentRelation->getToContent()
                && $newRelations->doesntContain($currentRelation->getToContent()->getId())
            ) {
                $currentRelation->getToContent()->removeRelationsToThisContent($currentRelation);
            }
            if (
                $currentRelation->getFromContent()
                && $newRelations->doesntContain($currentRelation->getFromContent()->getId())
            ) {
                $currentRelation->getFromContent()->removeRelationsFromThisContent($currentRelation);
            }
            $currentRelations = $currentRelations->filter(
                static function (Relation $r) use ($currentRelation) {
                    return $r !== $currentRelation;
                }
            );
            $this->em->remove($currentRelation);
        }

        // Then (re-) add selected ones
        foreach ($newRelations as $position => $id) {
            if ($currentRelationIds->contains($id)) {
                // If this relation already exists, don't add it a second time. Do set a proper order on it, though.
                $currentRelations
                    ->first(
                        static function (Relation $relation) use ($id) {
                            $fromId = $relation->getFromContent() ? $relation->getFromContent()->getId() : null;
                            $toId = $relation->getToContent() ? $relation->getToContent()->getId() : null;
                            return \in_array(
                                $id,
                                [$fromId, $toId],
                                true
                            );
                        }
                    )
                    ->setPosition($position);
                continue;
            }

            $contentTo = $this->contentRepository->findOneBy(['id' => $id]);
            if ($contentTo === null) {
                // Don't add relations to things that have gone missing
                continue;
            }

            $relation = new Relation($content, $contentTo);
            $relation->setPosition($position);
            $this->em->persist($relation);
        }
    }

    private function getEditLocale(Content $content): string
    {
        $locale = $this->getFromRequest('edit_locale', '');
        $locales = $content->getLocales();

        if ($locales->contains($locale) === false) {
            $locale = $content->getDefaultLocale();
        }

        if (! $locale) {
            $locale = $this->defaultLocale;
        }

        return $locale;
    }

    private function getPostedLocale(array $post): ?string
    {
        return $post['_edit_locale'] ?? null;
    }

    private function renderEditor(Content $content, $errors = null): Response
    {
        $twigvars = [
            'record' => $content,
            'locales' => $content->getLocales(),
            'defaultlocale' => $this->defaultLocale,
            'currentlocale' => $this->getEditLocale($content),
        ];

        if ($errors) {
            $twigvars['errors'] = $errors;
        }

        return $this->render('@bolt/content/edit.html.twig', $twigvars);
    }
}

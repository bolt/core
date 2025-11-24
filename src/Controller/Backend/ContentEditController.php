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
use Bolt\Entity\Field\MediaAwareInterface;
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
use Exception;
use Illuminate\Support\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * CRUD + status, duplicate, for content - note that listing is handled by ListingController.php
 */
class ContentEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    public function __construct(
        private TaxonomyRepository $taxonomyRepository,
        private RelationRepository $relationRepository,
        private ContentRepository $contentRepository,
        private MediaRepository $mediaRepository,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private ContentFillListener $contentFillListener,
        private EventDispatcherInterface $dispatcher,
        protected string $defaultLocale,
        private TranslatorInterface $translator,
        private ContentHelper $contentHelper
    ) {
    }

    #[Route(path: '/new/{contentType}', name: 'bolt_content_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, string $contentType, ?ContentValidatorInterface $contentValidator = null): Response
    {
        $content = new Content();

        /** @var User $user */
        $user = $this->getUser();

        $content->setAuthor($user);
        $content->setContentType($contentType);

        // content now has a contentType -> permission check possible
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CREATE, $content);

        $this->contentFillListener->fillContent($content);

        if ($request->getMethod() === 'POST') {
            $content->setPublishedAt(null);
            $content->setDepublishedAt(null);

            return $this->save($request, $content, $contentValidator);
        }

        return $this->edit($request, $content);
    }

    #[Route(path: '/edit/{id}', name: 'bolt_content_edit', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function edit(Request $request, Content $content): Response
    {
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_EDIT, $content);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::ON_EDIT);

        return $this->renderEditor($request, $content);
    }

    #[Route(path: '/edit/{id}', name: 'bolt_content_edit_post', requirements: ['id' => '\d+'], methods: [Request::METHOD_POST])]
    public function save(Request $request, ?Content $originalContent = null, ?ContentValidatorInterface $contentValidator = null): Response
    {
        $this->validateCsrf($request, 'editrecord');

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
            if ($originalStatus !== $content->getStatus()) {
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

                return $this->renderEditor($request, $content, $constraintViolations);
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
            'edit_locale' => $this->getEditLocale($request, $content) ?: null,
        ];
        $url = $this->urlGenerator->generate('bolt_content_edit', $urlParams);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::POST_SAVE);

        $locale = $originalAuthor->getLocale();

        // If we're "Saving Ajaxy"
        if ($request->isXmlHttpRequest()) {
            $modified = sprintf(
                '(%s: %s)',
                $this->translator->trans('field.modifiedAt', [], null, $locale),
                $this->contentHelper->get($content, '{modifiedAt}')
            );

            return new JsonResponse(
                [
                    'url' => $url,
                    'status' => 'success',
                    'type' => $this->translator->trans('success', [], null, $locale),
                    'message' => $this->translator->trans('content.updated_successfully', [], null, $locale),
                    'notification' => $this->translator->trans('flash_messages.notification', [], null, $locale),
                    'title' => $content->getExtras()['title'],
                    'modified' => $modified,
                ],
                Response::HTTP_OK
            );
        }

        // Otherwise, treat it as a normal POST-request cycle..
        $this->addFlash('success', 'content.updated_successfully');

        return new RedirectResponse($url);
    }

    #[Route(path: '/duplicate/{id}', name: 'bolt_content_duplicate', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function duplicate(Request $request, Content $content): Response
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
            'currentlocale' => $this->getEditLocale($request, $content),
            'defaultlocale' => $this->defaultLocale,
        ];

        return $this->render('@bolt/content/edit.html.twig', $twigvars);
    }

    #[Route(path: '/duplicate/{id}', name: 'bolt_content_duplicate_post', requirements: ['id' => '\d+'], methods: [Request::METHOD_POST])]
    public function duplicateSave(Request $request, ?Content $content = null): Response
    {
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CREATE, $content);

        return $this->new($request, $content->getContentType() ?? throw new NotFoundHttpException());
    }

    #[Route(path: '/status/{id}', name: 'bolt_content_status', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function status(Request $request, Content $content): Response
    {
        $this->validateCsrf($request, 'status');

        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_CHANGE_STATUS, $content);

        $content->setStatus($this->getFromRequest($request, 'status'));

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

    #[Route(path: '/delete/{id}', name: 'bolt_content_delete', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function delete(Request $request, Content $content): Response
    {
        $this->validateCsrf($request, 'delete');

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
        $request = $this->getRequest();
        $formData = $request->request->all();
        $locale = $this->getPostedLocale($formData) ?: $content->getDefaultLocale();

        /** @var User $user */
        $user = $this->getUser();

        if ($content === null) {
            $content = new Content();
            $content->setAuthor($user);
            $content->setContentType($request->attributes->get('id'));
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
                if (mb_strpos((string) $fieldName, 'sortorder') !== false) {
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
        $collections = $content->getFields()->filter(fn (Field $field): bool => $field->getType() === CollectionField::TYPE);

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
        /** @var Field|null $field */
        $field = null;

        $definition = empty($fieldDefinition) ? $content->getDefinition()->get('fields')->get($fieldName) : $fieldDefinition;

        if (empty($definition)) {
            throw new Exception("Content type `{$content->getContentType()}` doesn't have field `{$fieldName}`.");
        }

        if ($content->hasField($fieldName)) {
            $field = $content->getField($fieldName);
        }

        // If the Field exists, but it has the wrong type, we'll remove the existing one.
        if (($field !== null) && ! $content->hasField($fieldName, true)) {
            $content->removeField($field);
            try {
                $this->em->remove($field);
            } catch (ORMInvalidArgumentException) {
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
        if ($field instanceof MediaAwareInterface) {
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
                static fn (Relation $relation): ?int => $relation->getFromContent() === $content
                    ? $relation->getToContent()->getId()
                    : $relation->getFromContent()->getId()
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
                static fn (Relation $r): bool => $r !== $currentRelation
            );
            $this->em->remove($currentRelation);
        }

        // Then (re-) add selected ones
        foreach ($newRelations as $position => $id) {
            if ($currentRelationIds->contains($id)) {
                // If this relation already exists, don't add it a second time. Do set a proper order on it, though.
                $currentRelations
                    ->first(
                        static function (Relation $relation) use ($id): bool {
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

    private function getEditLocale(Request $request, Content $content): string
    {
        $locale = $this->getFromRequest($request, 'edit_locale', '');
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

    private function renderEditor(Request $request, Content $content, $errors = null): Response
    {
        $twigvars = [
            'record' => $content,
            'locales' => $content->getLocales(),
            'defaultlocale' => $this->defaultLocale,
            'currentlocale' => $this->getEditLocale($request, $content),
        ];

        if ($errors) {
            $twigvars['errors'] = $errors;
        }

        return $this->render('@bolt/content/edit.html.twig', $twigvars);
    }
}

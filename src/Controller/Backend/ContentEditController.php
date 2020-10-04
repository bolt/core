<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
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
use Bolt\Utils\TranslationsManager;
use Bolt\Validator\ContentValidatorInterface;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tightenco\Collect\Support\Collection;

/**
 * @Security("is_granted('ROLE_ADMIN')")
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

    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        RelationRepository $relationRepository,
        ContentRepository $contentRepository,
        MediaRepository $mediaRepository,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ContentFillListener $contentFillListener,
        CsrfTokenManagerInterface $csrfTokenManager,
        EventDispatcherInterface $dispatcher,
        string $defaultLocale
    ) {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->relationRepository = $relationRepository;
        $this->contentRepository = $contentRepository;
        $this->mediaRepository = $mediaRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->contentFillListener = $contentFillListener;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->dispatcher = $dispatcher;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @Route("/new/{contentType}", name="bolt_content_new", methods={"GET|POST"})
     */
    public function new(string $contentType): Response
    {
        $content = new Content();

        /** @var User $user */
        $user = $this->getUser();

        $content->setAuthor($user);
        $content->setContentType($contentType);
        $this->contentFillListener->fillContent($content);

        if ($this->request->getMethod() === 'POST') {
            return $this->save($content);
        }

        return $this->edit($content);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function edit(Content $content): Response
    {
        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::ON_EDIT);

        return $this->renderEditor($content);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit_post", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function save(?Content $content = null, ?ContentValidatorInterface $contentValidator = null): Response
    {
        $this->validateCsrf('editrecord');

        [$content, $relations] = $this->contentFromPost($content);

        if ($contentValidator) {
            // Question: do we want to validate the formData, or do we want to validate the content created
            // based on the formdata?
            // currently we do the latter.
//            $formData = $this->request->request->all();
//            $locale = $this->getPostedLocale($formData) ?: $content->getDefaultLocale();

            // about relations:
            // there might be weird edge-cases when a relation is added in the form, but not
            // existing in the db anymore, combined with min/max validation rules that will fail/succeed
            // based on the number of relations that have actually been created vs the number of relations
            // that are in the form of the user.
            // This should only be an issue of date is being deleted from another place while an end-user
            // is creating content via the backend forms.
            $constraintViolations = $contentValidator->validate($content, $relations);
            if (count($constraintViolations) > 0) {
                return $this->renderEditor($content, $constraintViolations);
            }
        }

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_SAVE);

        /* Note: Doctrine also calls preUpdate() -> Event/Listener/FieldFillListener.php */
        $this->em->persist($content);
        $this->em->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $urlParams = [
            'id' => $content->getId(),
            'edit_locale' => $this->getEditLocale($content) ?: null,
        ];
        $url = $this->urlGenerator->generate('bolt_content_edit', $urlParams);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::POST_SAVE);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/preview/{id}", name="bolt_content_edit_preview", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function preview(?Content $content = null): Response
    {
        $this->validateCsrf('editrecord');

        [$content] = $this->contentFromPost($content);
        $recordSlug = $content->getDefinition()->get('singular_slug');

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::ON_PREVIEW);

        $context = [
            'record' => $content,
            $recordSlug => $content,
        ];

        $templates = $this->templateChooser->forRecord($content);

        return $this->render($templates, $context);
    }

    /**
     * @Route("/duplicate/{id}", name="bolt_content_duplicate", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function duplicate(Content $content): Response
    {
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
        return $this->new($content->getContentType());
    }

    /**
     * @Route("/status/{id}", name="bolt_content_status", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function status(Content $content): Response
    {
        $this->validateCsrf('status');

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

    private function contentFromPost(?Content $content): array
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

        // @todo dumb status validation, to be replaced with Symfony Form validation
        $status = Json::findScalar($formData['status']);
        if (in_array($status, Statuses::all(), true) === true) {
            $content->setStatus($status);
        }

        $content->setPublishedAt(! empty($formData['publishedAt']) ? new Carbon($formData['publishedAt']) : null);
        $content->setDepublishedAt(! empty($formData['depublishedAt']) ? new Carbon($formData['depublishedAt']) : null);

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
                $this->updateTaxonomy($content, $fieldName, $taxonomy);
            }
        }

        $relations = [];
        if (isset($formData['relationship'])) {
            foreach ($formData['relationship'] as $relationTypeName => $relation) {
                $relations[$relationTypeName] = $this->updateRelation($content, $relation);
            }
        }

        return [$content, $relations];
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

    private function updateCollections(Content $content, array $formData, ?string $locale): void
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

                foreach ($collectionItems as $name => $instances) {
                    // order field is only used to determine the order in which fields are submitted
                    if ($name === 'order') {
                        continue;
                    }

                    foreach ($instances as $orderId => $value) {
                        $order = $orderArray[$orderId];
                        $fieldDefinition = $collection->getDefinition()->get('fields')->get($name);
                        $field = FieldRepository::factory($fieldDefinition, $name);
                        $field->setParent($collection);
                        $field->setSortorder($order);
                        $content->addField($field);
                        $this->updateField($field, $value, $locale);
                        $tm->applyTranslations($field, $collectionName, $orderId);
                    }
                }
            }
        }
    }

    private function getFieldToUpdate(Content $content, string $fieldName, $fieldDefinition = ''): Field
    {
        /** @var Field $field */
        $field = null;

        $definition = empty($fieldDefinition) ? $content->getDefinition()->get('fields')->get($fieldName) : $fieldDefinition;

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

    private function updateField(Field $field, $value, ?string $locale): void
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
                $child = $field->getValueForEditor()[$name];

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

    private function updateTaxonomy(Content $content, string $key, $taxonomy): void
    {
        $taxonomy = (new Collection(Json::findArray($taxonomy)))->filter();

        // Remove old ones
        foreach ($content->getTaxonomies($key) as $current) {
            $content->removeTaxonomy($current);
        }

        // Then (re-) add selected ones
        foreach ($taxonomy as $slug) {
            $taxonomy = $this->taxonomyRepository->findOneBy([
                'type' => $key,
                'slug' => $slug,
            ]);

            if ($taxonomy === null) {
                $taxonomy = $this->taxonomyRepository->factory($key, $slug);
            }

            $content->addTaxonomy($taxonomy);
        }
    }

    private function updateRelation(Content $content, $newRelations): array
    {
        $newRelations = (new Collection(Json::findArray($newRelations)))->filter();
        $currentRelations = $this->relationRepository->findRelations($content, null, true, null, false);
        $relationsResult = [];

        // Remove old ones
        foreach ($currentRelations as $currentRelation) {
            $this->em->remove($currentRelation);
        }

        // Then (re-) add selected ones
        foreach ($newRelations as $id) {
            $contentTo = $this->contentRepository->findOneBy(['id' => $id]);

            if ($contentTo === null) {
                // Don't add relations to things that have gone missing
                continue;
            }

            $relation = new Relation($content, $contentTo);

            $this->em->persist($relation);
            $relationsResult[] = $id;
        }

        return $relationsResult;
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
        return $post['_edit_locale'] ?: null;
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

<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Taxonomy;
use Bolt\Enum\Statuses;
use Bolt\EventListener\ContentFillListener;
use Bolt\Repository\TaxonomyRepository;
use Bolt\TemplateChooser;
use Carbon\Carbon;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class ContentEditController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContentEditController extends BaseController
{
    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /** @var ObjectManager */
    private $em;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        Config $config,
        CsrfTokenManagerInterface $csrfTokenManager,
        ObjectManager $em,
        UrlGeneratorInterface $urlGenerator,
        TemplateChooser $templateChooser)
    {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        parent::__construct($config, $csrfTokenManager);
        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route("/new/{contentType}", name="bolt_content_new", methods={"GET"})
     */
    public function new(string $contentType, Request $request, ContentFillListener $contentListener): Response
    {
        $content = new Content();
        $content->setAuthor($this->getUser());
        $content->setContentType($contentType);
        $contentListener->fillContent($content);

        return $this->edit($request, $content);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function edit(Request $request, Content $content): Response
    {
        $twigvars = [
            'record' => $content,
            'locales' => $content->getLocales(),
            'currentlocale' => $this->getEditLocale($request, $content),
        ];

        return $this->renderTemplate('content/edit.html.twig', $twigvars);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit_post", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function editPost(Request $request, ?Content $content = null): Response
    {
        $this->validateToken($request);

        $content = $this->contentFromPost($content, $request);

        $this->em->persist($content);
        $this->em->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $urlParams = [
            'id' => $content->getId(),
            'edit_locale' => $this->getEditLocale($request, $content) ?: null,
        ];
        $url = $this->urlGenerator->generate('bolt_content_edit', $urlParams);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/viewsaved/{id}", name="bolt_content_edit_viewsave", methods={"POST"})
     */
    public function editViewSaved(Request $request, UrlGeneratorInterface $urlGenerator, ?Content $content = null): Response
    {
        $this->validateToken($request);

        $urlParams = [
            'slugOrId' => $content->getId(),
            'contentTypeSlug' => $content->getDefinition()->get('slug'),
        ];

        $url = $urlGenerator->generate('record', $urlParams);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/preview/{id}", name="bolt_content_edit_preview", methods={"POST"})
     */
    public function editPreview(Request $request, ?Content $content = null): Response
    {
        $this->validateToken($request);

        $content = $this->contentFromPost($request, $content);
        $recordSlug = $content->getDefinition()->get('singular_slug');

        $context = [
            'record' => $content,
            $recordSlug => $content,
        ];

        $templates = $this->templateChooser->record($content);

        return $this->renderTemplate($templates, $context);
    }

    private function validateToken(Request $request): void
    {
        $token = new CsrfToken('editrecord', $request->request->get('_csrf_token'));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }

    private function contentFromPost(?Content $content, Request $request): Content
    {
        $formData = $request->request->all();

        $locale = $this->getPostedLocale($formData);

        if (! $content) {
            $content = new Content();
            $content->setAuthor($this->getUser());
            $content->setContentType($request->attributes->get('id'));
            $content->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        }

        // @todo dumb status validation, to be replaced with Symfony Form validation
        $status = Json::findScalar($formData['status']);
        if (in_array($status, Statuses::all(), true)) {
            $content->setStatus($status);
        }

        $content->setPublishedAt(new Carbon($formData['publishedAt']));
        $content->setDepublishedAt(new Carbon($formData['depublishedAt']));

        foreach ($formData['fields'] as $fieldName => $fieldValue) {
            $this->updateFieldFromPost($fieldName, $fieldValue, $content, $locale);
        }

        if (isset($formData['taxonomy'])) {
            foreach ($formData['taxonomy'] as $fieldName => $taxonomy) {
                $this->updateTaxonomyFromPost($fieldName, $taxonomy, $content);
            }
        }

        return $content;
    }

    private function updateFieldFromPost(string $key, $postfield, Content $content, ?string $locale): void
    {
        if ($content->hasField($key)) {
            $field = $content->getField($key);
            if ($field->getDefinition()->get('localize')) {
                // load translated field
                $field->setLocale($locale);
                $this->em->refresh($field);
            }
        } else {
            $fields = collect($content->getDefinition()->get('fields'));
            $field = Field::factory($fields->get($key), $key);
            $field->setName($key);
            $content->addField($field);
            if ($field->getDefinition()->get('localize')) {
                $field->setLocale($locale);
            }
        }

        // If the value is an array that contains a string of JSON, parse it
        if (is_iterable($postfield) && Json::test(current($postfield))) {
            $postfield = Json::findArray($postfield);
        }

        $field->setValue((array) $postfield);
    }

    private function updateTaxonomyFromPost(string $key, $taxonomy, Content $content): void
    {
        $taxonomy = collect(Json::findArray($taxonomy))->filter();

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

            if (! $taxonomy) {
                $taxonomy = Taxonomy::factory($key, $slug);
            }

            $content->addTaxonomy($taxonomy);
        }
    }

    private function getEditLocale(Request $request, Content $content): string
    {
        $locale = $request->query->get('edit_locale', '');
        $locales = $content->getLocales();

        if ($locales->contains($locale) === false) {
            $locale = $content->getDefaultLocale();
        }

        return $locale;
    }

    private function getPostedLocale(array $post): ?string
    {
        return $post['_edit_locale'] ?: null;
    }
}

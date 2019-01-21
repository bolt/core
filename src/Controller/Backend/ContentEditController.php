<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Taxonomy;
use Bolt\Repository\TaxonomyRepository;
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
    /**
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;

    public function __construct(TaxonomyRepository $taxonomyRepository, Config $config, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->taxonomyRepository = $taxonomyRepository;
        parent::__construct($config, $csrfTokenManager);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit", methods={"GET"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function edit(string $id, Request $request, ?Content $content = null): Response
    {
        if (! $content) {
            $content = new Content();
            $content->setAuthor($this->getUser());
            $content->setContentType($id);
            $content->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        }

        $twigvars = [
            'record' => $content,
            'locales' => $content->getLocales(),
            'currentlocale' => $this->getEditLocale($request, $content),
        ];

        return $this->renderTemplate('content/edit.html.twig', $twigvars);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit_post", methods={"POST"})
     */
    public function editPost(Request $request, ObjectManager $manager, UrlGeneratorInterface $urlGenerator, ?Content $content = null): Response
    {
        $token = new CsrfToken('editrecord', $request->request->get('_csrf_token'));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $content = $this->contentFromPost($content, $request);

        $manager->persist($content);
        $manager->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $urlParams = [
            'id' => $content->getId(),
            'locale' => $this->getEditLocale($request, $content) ?: null,
        ];
        $url = $urlGenerator->generate('bolt_content_edit', $urlParams);

        return new RedirectResponse($url);
    }

    private function contentFromPost(?Content $content, Request $request): Content
    {
        $post = $request->request->all();

        $locale = $this->getPostedLocale($post);

        if (! $content) {
            $content = new Content();
            $content->setAuthor($this->getUser());
            $content->setContentType($request->attributes->get('id'));
            $content->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        }

        $content->setStatus(Json::findScalar($post['status']));
        $content->setPublishedAt(new Carbon($post['publishedAt']));
        $content->setDepublishedAt(new Carbon($post['depublishedAt']));

        foreach ($post['fields'] as $key => $postfield) {
            $this->updateFieldFromPost($key, $postfield, $content, $locale);
        }

        if (isset($post['taxonomy'])) {
            foreach ($post['taxonomy'] as $key => $taxonomy) {
                $this->updateTaxonomyFromPost($key, $taxonomy, $content);
            }
        }

        return $content;
    }

    private function updateFieldFromPost(string $key, $postfield, Content $content, string $locale): void
    {
        if ($content->hasLocalizedField($key, $locale)) {
            $field = $content->getLocalizedField($key, $locale);
        } else {
            $fields = collect($content->getDefinition()->get('fields'));
            $field = Field::factory($fields->get($key), $key);
            $field->setName($key);
            $content->addField($field);
        }

        // If the value is an array that contains a string of JSON, parse it
        if (is_iterable($postfield) && Json::test(current($postfield))) {
            $postfield = Json::findArray($postfield);
        }

        $field->setValue((array) $postfield);

        if ($field->getDefinition()->get('localize')) {
            $field->setLocale($locale);
        } else {
            $field->setLocale('');
        }
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
        $locale = $request->query->get('locale', '');
        $locales = $content->getLocales();

        if (! $locales->contains($locale)) {
            $locale = $locales->first();
        }

        return $locale;
    }

    private function getPostedLocale(array $post): string
    {
        return $post['_edit_locale'] ?: '';
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Content\TaxonomyType;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaxonomyController extends TwigAwareController implements FrontendZoneInterface
{
    #[Route(path: '/{taxonomyslug}/{slug}', name: 'taxonomy', requirements: [
        'taxonomyslug' => '%bolt.requirement.taxonomies%',
    ], methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[Route(path: '/{_locale}/{taxonomyslug}/{slug}', name: 'taxonomy_locale', requirements: [
        'taxonomyslug' => '%bolt.requirement.taxonomies%',
        '_locale' => '%app_locales%',
    ], methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function listing(Request $request, ContentRepository $contentRepository, string $taxonomyslug, string $slug): Response
    {
        $page = (int) $this->getFromRequest($request, 'page', '1');
        $amountPerPage = $this->config->get('general/listing_records');

        $taxonomy = TaxonomyType::factory($taxonomyslug, $this->config->get('taxonomies'));

        if ($taxonomy->has('listing_records')) {
            $amountPerPage = $taxonomy->get('listing_records');
        }

        $records = $contentRepository->findForTaxonomy($page, $taxonomy, $slug, $amountPerPage);

        $this->canonical->setPath(
            'taxonomy_locale',
            [
                'taxonomyslug' => $taxonomy->get('slug'),
                '_locale' => $request->getLocale(),
                'slug' => $slug,
            ]
        );

        $templates = $this->templateChooser->forTaxonomy($taxonomy);

        $twigVars = [
            'records' => $records,
            'taxonomy' => $taxonomy,
            'slug' => $slug,
        ];

        return $this->render($templates, $twigVars);
    }
}

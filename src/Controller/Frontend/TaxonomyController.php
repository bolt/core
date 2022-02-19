<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Content\TaxonomyType;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Routing\DynamicRouteLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaxonomyController extends TwigAwareController implements FrontendZoneInterface
{
    /**
     * @see DynamicRouteLoader for routes to this method.
     */
    public function listing(ContentRepository $contentRepository, string $taxonomyslug, string $slug): Response
    {
        $page = (int) $this->getFromRequest('page', '1');
        $amountPerPage = $this->config->get('general/listing_records');

        $taxonomy = TaxonomyType::factory($taxonomyslug, $this->config->get('taxonomies'));

        /** @var Content[] $records */
        $records = $contentRepository->findForTaxonomy($page, $taxonomy, $slug, $amountPerPage);

        $this->canonical->setPath(
            'taxonomy_locale',
            [
                'taxonomyslug' => $taxonomy->get('slug'),
                '_locale' => $this->request->getLocale(),
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

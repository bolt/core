<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaxonomyController extends TwigAwareController implements FrontendZoneInterface
{
    /**
     * @Route(
     *     "/{taxonomyslug}/{slug}",
     *     name="taxonomy",
     *     requirements={"taxonomyslug"="%bolt.requirement.taxonomies%"},
     *     methods={"GET|POST"}
     * )
     * @Route(
     *     "/{_locale}/{taxonomyslug}/{slug}",
     *     name="taxonomy_locale",
     *     requirements={"taxonomyslug"="%bolt.requirement.taxonomies%", "_locale": "%app_locales%"},
     *     methods={"GET|POST"}
     * )
     */
    public function listing(ContentRepository $contentRepository, string $taxonomyslug, string $slug): Response
    {
        $page = (int) $this->getFromRequest('page', '1');
        $amountPerPage = $this->config->get('general/listing_records');

        $taxonomy = $this->config->getTaxonomy($taxonomyslug);

        /** @var Content[] $records */
        $records = $contentRepository->findForTaxonomy($page, $taxonomy, $slug, $amountPerPage);

        $templates = $this->templateChooser->forTaxonomy($taxonomyslug);

        $twigVars = [
            'records' => $records,
            'taxonomy' => $taxonomy,
            'slug' => $slug,
        ];

        return $this->render($templates, $twigVars);
    }
}

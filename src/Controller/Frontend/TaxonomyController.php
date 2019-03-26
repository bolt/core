<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class TaxonomyController extends TwigAwareController
{
    /**
     * @var TemplateChooser
     */
    private $templateChooser;

    public function __construct(Config $config, Environment $twig, TemplateChooser $templateChooser)
    {
        parent::__construct($config, $twig);

        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route(
     *     "/{taxonomyslug}/{slug}",
     *     name="taxonomy",
     *     requirements={"taxonomyslug"="%bolt.requirement.taxonomies%"},
     *     methods={"GET"}
     * )
     */
    public function listing(ContentRepository $contentRepository, Request $request, string $taxonomyslug, string $slug): Response
    {
        $page = (int) $request->query->get('page', 1);
        $amountPerPage = $this->config->get('general/listing_records');

        /** @var Content[] $records */
        $records = $contentRepository->findForTaxonomy($page, $taxonomyslug, $slug, $amountPerPage);

        $templates = $this->templateChooser->forTaxonomy($taxonomyslug);

        return $this->renderTemplate($templates, ['records' => $records]);
    }
}

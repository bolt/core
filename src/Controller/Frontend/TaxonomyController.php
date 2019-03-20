<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Content\ContentType;
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

        $contentType = ContentType::factory('page', $this->config->get('contenttypes'));

        /** @var Content[] $records */
        $records = $contentRepository->findForTaxonomy($page, $taxonomyslug, $slug);

        $templates = $this->templateChooser->forListing($contentType);

        return $this->renderTemplate($templates, ['records' => $records]);



    }
}

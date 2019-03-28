<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class SearchController extends TwigAwareController
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
     * @Route("/search", methods={"GET", "POST"}, name="search")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function search(ContentRepository $contentRepository, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $search = $request->get('search', '');
        $amountPerPage = $this->config->get('general/listing_records');

        // @todo implement actual Search Engine
        if (! empty($search)) {
            $records = $contentRepository->searchNaive($search, $page, $amountPerPage);
        } else {
            $records = new Pagerfanta(new ArrayAdapter([]));
        }

        $context = [
            'search' => $search,
            'records' => $records,
        ];

        $templates = $this->templateChooser->forSearch();

        return $this->renderTemplate($templates, $context);
    }
}

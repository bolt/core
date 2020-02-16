<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Bolt\Utils\Sanitiser;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends TwigAwareController implements FrontendZoneInterface
{
    /** @var TemplateChooser */
    private $templateChooser;

    /** @var Sanitiser */
    private $sanitiser;

    public function __construct(TemplateChooser $templateChooser, Sanitiser $sanitiser)
    {
        $this->templateChooser = $templateChooser;
        $this->sanitiser = $sanitiser;
    }

    /**
     * @Route("/search", methods={"GET|POST"}, name="search")
     * @Route("/{_locale}/search", methods={"GET|POST"}, name="search_locale")
     */
    public function search(ContentRepository $contentRepository, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchTerm = $request->get('searchTerm', $request->get('search', $request->get('q', '')));
        $searchTerm = $this->sanitiser->clean($searchTerm);
        $amountPerPage = $this->config->get('general/listing_records');

        // @todo implement actual Search Engine
        if (! empty($searchTerm)) {
            $records = $contentRepository->searchNaive($searchTerm, $page, $amountPerPage);
        } else {
            $records = new Pagerfanta(new ArrayAdapter([]));
        }

        $context = [
            'searchTerm' => $searchTerm,
            'search' => $searchTerm, // Keep 'search' for Backwards Compatibility
            'records' => $records,
        ];

        $templates = $this->templateChooser->forSearch();

        return $this->renderTemplate($templates, $context);
    }
}

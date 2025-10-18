<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends TwigAwareController implements FrontendZoneInterface
{
    #[Route(path: '/search', name: 'search', methods: ['GET|POST'])]
    #[Route(path: '/{_locale}/search', name: 'search_locale', methods: ['GET|POST'])]
    public function search(ContentRepository $contentRepository): Response
    {
        $page = (int) $this->getFromRequest('page', '1');
        $searchTerm = $this->getFromRequestArray(['searchTerm', 'search', 'q'], '');
        $amountPerPage = (int) $this->config->get('general/search_results_records');

        // Just the ContentTypes that have `searchable: true`
        $contentTypes = $this->config->get('contenttypes')->where('searchable', true);

        // @todo implement actual Search Engine
        if (! empty($searchTerm)) {
            $records = $contentRepository->searchNaive($searchTerm, $page, $amountPerPage, $contentTypes);
        } else {
            $records = new Pagerfanta(new ArrayAdapter([]));
        }

        $context = [
            'searchTerm' => $searchTerm,
            // Keep 'search' for Backwards Compatibility
            'search' => $searchTerm,
            'records' => $records,
        ];

        $templates = $this->templateChooser->forSearch();

        return $this->render($templates, $context);
    }
}

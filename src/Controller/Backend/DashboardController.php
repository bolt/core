<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Storage\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends TwigAwareController implements BackendZoneInterface
{
    #[Route(path: '/', name: 'bolt_dashboard', methods: [Request::METHOD_GET])]
    public function index(Query $query): Response
    {
        $this->denyAccessUnlessGranted('dashboard');

        // TODO PERMISSIONS: implement listing that only lists content that the user is allowed to see
        $amount = (int) $this->config->get('general/records_per_page', 10);
        $page = (int) $this->request->get('page', 1);
        $contentTypes = $this->config->get('contenttypes')->where('show_on_dashboard', true)->keys()->implode(',');
        $filter = strip_tags((string) $this->getFromRequest('filter', ''));

        $pager = $this->createPager($query, $contentTypes, $amount, '-modifiedAt');
        $nbPages = $pager->getNbPages();

        if ($page > $nbPages) {
            return $this->redirectToRoute('bolt_dashboard');
        }

        $records = $pager->setCurrentPage($page);

        return $this->render('@bolt/pages/dashboard.html.twig', [
            'records' => $records,
            'filter_value' => $filter,
        ]);
    }
}

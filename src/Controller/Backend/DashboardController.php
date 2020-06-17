<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Storage\Query;
use Bolt\Utils\Html;
use Bolt\Utils\Sanitiser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class DashboardController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/", name="bolt_dashboard", methods={"GET"})
     */
    public function index(Query $query, Request $request): Response
    {
        $amount = (int) $this->config->get('general/records_per_page', 10);
        $page = (int) $request->get('page', 1);
        $contentTypes = $this->config->get('contenttypes')->where('show_on_dashboard', true)->keys()->implode(',');
        $filter = $this->getFromRequest('filter');

        $pager = $this->createPager($request, $query, $contentTypes, $amount, '-modifiedAt');
        $nbPages = $pager->getNbPages();

        if ($page > $nbPages) {
            return $this->redirectToRoute('bolt_dashboard');
        }

        $records = $pager->setCurrentPage($page);

        return $this->renderTemplate('@bolt/pages/dashboard.html.twig', [
            'records' => $records,
            'filter_value' => $filter,
        ]);
    }
}

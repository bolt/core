<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
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
    public function index(ContentRepository $content, Request $request): Response
    {
        $amount = $this->config->get('general/records_per_page', 10);
        $page = (int) $request->get('page', 1);

        /** @var Content $records */
        $records = $content->findLatest(null, $page, $amount);

        return $this->renderTemplate('@bolt/pages/dashboard.html.twig', [
            'records' => $records,
        ]);
    }
}

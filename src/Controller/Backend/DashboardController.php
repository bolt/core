<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Config;
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
    public function index(ContentRepository $content, Request $request, Config $config): Response
    {
        $amount = (int) $this->config->get('general/records_per_page', 10);
        $page = (int) $request->get('page', 1);
        $contentTypes = $config->get('contenttypes');

        /** @var Content $records */
        $records = $content->findLatest($contentTypes, $page, $amount);

        return $this->renderTemplate('@bolt/pages/dashboard.html.twig', [
            'records' => $records,
        ]);
    }
}

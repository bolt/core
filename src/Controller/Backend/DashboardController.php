<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class DashboardController extends BaseController
{
    /**
     * @Route("/", name="bolt_dashboard", methods={"GET"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(ContentRepository $content): Response
    {
        /** @var Content $records */
        $records = $content->findLatest();

        return $this->renderTemplate('dashboard/dashboard.html.twig', [
            'records' => $records,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class DashboardController extends TwigAwareController
{
    /**
     * @Route("/", name="bolt_dashboard", methods={"GET"})
     */
    public function index(ContentRepository $content): Response
    {
        /** @var Content $records */
        $records = $content->findLatest();

        return $this->renderTemplate('@bolt/dashboard/dashboard.html.twig', [
            'records' => $records,
        ]);
    }
}

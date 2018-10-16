<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BackendController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BackendController extends BaseController
{
    /**
     * @Route("/", name="bolt_dashboard")
     * was: ("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).+"}, name="index")
     *
     * @param ContentRepository $content
     *
     * @return Response
     */
    public function index(ContentRepository $content)
    {
        /** @var Content $records */
        $records = $content->findLatest();

        return $this->renderTemplate('dashboard/dashboard.twig', [
            'records' => $records,
        ]);
    }
}

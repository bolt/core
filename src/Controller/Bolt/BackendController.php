<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BackendController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BackendController extends AbstractController
{
    /** @var Config */
    private $config;

    /** @param Config $config */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

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
        $version = Version::VERSION;

        /** @var Content $records */
        $records = $content->findLatest();

        return $this->render('bolt/dashboard/dashboard.twig', [
            'records' => $records,
            'version' => $version,
        ]);
    }
}

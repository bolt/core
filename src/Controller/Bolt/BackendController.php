<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    /** @var Version */
    private $version;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/", name="bolt_dashboard")
     * was: ("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).+"}, name="index")
     * @param null|string $vueRouting
     * @return Response
     */
    public function index(?string $vueRouting = null, $name = 'Gekke Henkie')
    {
        $version = Version::VERSION;

        return $this->render('bolt/dashboard/dashboard.twig', [
            'vueRouting' => \is_null($vueRouting) ? '/' : '/' . $vueRouting,
            'name' => $name,
            'version' => $version,
        ]);
    }
}

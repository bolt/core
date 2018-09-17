<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Version;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/bolt")
     */
    /* public function index($name = 'Gekke Henkie')
    {
        $version = Version::VERSION;

        return $this->render('bolt/index.html.twig', [
             'name' => $name,
             'version' => $version,
         ]);
    } */

    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).+"}, name="index")
     * @param null|string $vueRouting
     * @return Response
     */
    public function index(?string $vueRouting = null, $name = 'Gekke Henkie')
    {
        $version = Version::VERSION;

        return $this->render('bolt/index.html.twig', [
            'vueRouting' => \is_null($vueRouting) ? '/' : '/' . $vueRouting,
            'name' => $name,
            'version' => $version,
        ]);
    }
}

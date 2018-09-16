<?php

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
    public function index($name = 'Gekke Henkie')
    {
        $version = Version::VERSION;

        return $this->render('bolt/index.html.twig', [
             'name' => $name,
             'version' => $version,
         ]);
    }
}

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

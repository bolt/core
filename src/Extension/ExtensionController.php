<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property ContainerInterface $container
 */
class ExtensionController extends AbstractController
{
    use ServicesTrait;
    use ConfigTrait;

    public function __construct(Config $config)
    {
        $this->boltConfig = $config;
    }
}

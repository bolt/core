<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExtensionController extends AbstractController
{
    public function __construct(Config $config)
    {
        $this->boltConfig = $config;
    }

    use ServicesTrait;
    use ConfigTrait;
}

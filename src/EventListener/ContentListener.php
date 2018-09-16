<?php

declare(strict_types=1);

namespace Bolt\EventListener;

use Bolt\Configuration\Config;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ContentListener
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (method_exists($entity, 'setConfig')) {
            $entity->setConfig($this->config);
        }
    }
}

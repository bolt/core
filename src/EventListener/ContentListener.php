<?php

declare(strict_types=1);

namespace Bolt\EventListener;

use Bolt\Configuration\Config;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContentListener
{
    /** @var Config */
    private $config;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(Config $config, UrlGeneratorInterface $urlGenerator)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (method_exists($entity, 'setConfig')) {
            $entity->setConfig($this->config);
        }
        if (method_exists($entity, 'setUrlGenerator')) {
            $entity->setUrlGenerator($this->urlGenerator);
        }
    }
}

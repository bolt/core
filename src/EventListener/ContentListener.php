<?php

declare(strict_types=1);

namespace Bolt\EventListener;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
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

    public function postLoad(LifecycleEventArgs $args): void
    {
        /** @var Content $entity */
        $entity = $args->getEntity();

        if (method_exists($entity, 'setDefinitionFromContentTypesConfig')) {
            $entity->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        }
        if (method_exists($entity, 'setUrlGenerator')) {
            $entity->setUrlGenerator($this->urlGenerator);
        }
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Twig\ContentExtension;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ContentFillListener
{
    /** @var Config */
    private $config;

    /** @var ContentExtension */
    private $contentExtension;

    public function __construct(Config $config, ContentExtension $contentExtension)
    {
        $this->config = $config;
        $this->contentExtension = $contentExtension;
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Content) {
            $this->fillContent($entity);
        }
    }

    public function fillContent(Content $entity): void
    {
        $entity->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        $entity->setContentExtension($this->contentExtension);
    }
}

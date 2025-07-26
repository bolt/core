<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Config;
use Bolt\Entity\Taxonomy;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TaxonomyFillListener
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Taxonomy) {
            $this->fillTaxonomy($entity);
        }
    }

    public function fillTaxonomy(Taxonomy $entity): void
    {
        $entity->setDefinitionFromTaxonomyTypesConfig($this->config->get('taxonomies'));
    }
}

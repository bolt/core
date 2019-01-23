<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Hydrator\ORM\ObjectHydrator;
use Gedmo\Translatable\Hydrator\ORM\SimpleObjectHydrator;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;

class TranslatableEntityManager extends EntityManager
{
    public function newHydrator($hydrationMode)
    {
        switch ($hydrationMode) {
            case Query::HYDRATE_OBJECT:
            case TranslationWalker::HYDRATE_OBJECT_TRANSLATION:
                return new ObjectHydrator($this);

            case Query::HYDRATE_SIMPLEOBJECT:
            case TranslationWalker::HYDRATE_SIMPLE_OBJECT_TRANSLATION:
                return new SimpleObjectHydrator($this);

            default:
                return parent::newHydrator($hydrationMode);
        }
    }

    public static function create($connection, Configuration $config, EventManager $eventManager = null)
    {
        if ( ! $config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }

        $connection = static::createConnection($connection, $config, $eventManager);

        return new self($connection, $config, $connection->getEventManager());
    }
}
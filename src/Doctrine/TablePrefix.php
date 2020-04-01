<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class TablePrefix
{
    use TablePrefixTrait;

    public function __construct($tablePrefixes, ManagerRegistry $managerRegistry)
    {
        $this->setTablePrefixes($tablePrefixes, $managerRegistry);
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        if ($tablePrefix = $this->getTablePrefix($eventArgs->getEntityManager())) {
            $classMetadata = $eventArgs->getClassMetadata();

            if (!$classMetadata->isInheritanceTypeSingleTable()
                || $classMetadata->getName() === $classMetadata->rootEntityName) {
                $classMetadata->setPrimaryTable(
                    [
                        'name' => $tablePrefix.$classMetadata->getTableName(),
                    ]
                );
            }

            foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
                if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
                    $mappedTableName = $mapping['joinTable']['name'];
                    $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $tablePrefix.$mappedTableName;
                }
            }
        }
    }
}

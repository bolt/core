<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Entity\FieldInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use LogicException;
use ReflectionClass;

/**
 * Greatly inspired by:
 *
 * @see https://medium.com/@jasperkuperus/defining-discriminator-maps-at-child-level-in-doctrine-2-1cd2ded95ffb
 */
class FieldDiscriminatorListener
{
    private MappingDriver $mappingDriver;

    /** The temporary map used for one run, when computing everything */
    private array $tempMap = [];

    /** The cached map, this holds the results after a computation, also for other classes */
    private array $map = [];

    /**
     * @throws ORMException
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->mappingDriver = $em->getConfiguration()->getMetadataDriverImpl() ?? throw new MappingException('Could not load mapping driver');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $className = $event->getClassMetadata()->name;
        if ($this->isField($className) === false) {
            return;
        }

        if (array_key_exists($className, $this->map) === false) {
            // Now build the whole temp map
            $this->checkFamily($className);

            // Create the lookup entries
            $discriminatorMap = array_flip($this->tempMap);
            foreach (array_keys($this->tempMap) as $className) {
                $this->map[$className] = $discriminatorMap;
            }
            // clear temp map
            $this->tempMap = [];
        }

        $event->getClassMetadata()->setDiscriminatorMap($this->map[$className]);
    }

    private function isField(string $class): bool
    {
        return is_subclass_of($class, FieldInterface::class);
    }

    private function extractFieldType(string $class): string
    {
        $fieldType = (new $class())->getType();
        if (in_array($fieldType, $this->tempMap, true) === true) {
            throw new LogicException("Found duplicate discriminator map entry '" . $fieldType . "' in " . $class);
        }

        return $fieldType;
    }

    private function checkFamily(string $className): void
    {
        $this->tempMap[$className] = $this->extractFieldType($className);
        $reflection = new ReflectionClass($className);
        $parentClass = $reflection->getParentClass();

        if ($parentClass !== false) {
            // Also check all the parents of our child
            $this->checkFamily($parentClass->name);
        } else {
            // Find all the children of this class
            $this->checkChildren($className);
        }
    }

    private function checkChildren(string $parentClassName): void
    {
        foreach ($this->mappingDriver->getAllClassNames() as $className) {
            // Haven't done this class yet? Go for it.
            if (is_subclass_of($className, $parentClassName)
                && $this->isField($className)
                && array_key_exists($className, $this->tempMap) === false
            ) {
                $this->tempMap[$className] = $this->extractFieldType($className);
                $this->checkChildren($className);
            }
        }
    }
}

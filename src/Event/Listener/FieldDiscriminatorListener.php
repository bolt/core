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
    private readonly MappingDriver $mappingDriver;

    /**
     * The temporary map used for one run, when computing everything
     *
     * @var array<class-string<FieldInterface>, string>
     */
    private array $tempMap = [];

    /**
     * The cached map, this holds the results after a computation, also for other classes
     *
     * @var array<class-string<FieldInterface>, array<string, class-string<FieldInterface>>>
     */
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
        /** @var class-string $className */
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

    /**
     * @param class-string $class
     * @phpstan-assert-if-true class-string<FieldInterface> $class
     */
    private function isField(string $class): bool
    {
        return is_subclass_of($class, FieldInterface::class);
    }

    /**
     * @param class-string<FieldInterface> $class
     */
    private function extractFieldType(string $class): string
    {
        $field = new $class();
        $fieldType = $field->getType();
        if (in_array($fieldType, $this->tempMap, true) === true) {
            throw new LogicException("Found duplicate discriminator map entry '" . $fieldType . "' in " . $class);
        }

        return $fieldType;
    }

    /**
     * @param class-string<FieldInterface> $className
     */
    private function checkFamily(string $className): void
    {
        $this->tempMap[$className] = $this->extractFieldType($className);
        $reflection = new ReflectionClass($className);
        $parentClass = $reflection->getParentClass();

        if ($parentClass !== false) {
            // Also check all the parents of our child

            /** @var class-string<FieldInterface> $parentClassName */
            $parentClassName = $parentClass->name;

            $this->checkFamily($parentClassName);
        } else {
            // Find all the children of this class
            $this->checkChildren($className);
        }
    }

    /**
     * @param class-string<FieldInterface> $parentClassName
     */
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

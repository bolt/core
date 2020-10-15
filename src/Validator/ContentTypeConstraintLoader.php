<?php


namespace Bolt\Validator;


use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AbstractLoader;

/**
 * Class ContentTypeConstraintLoader
 * @package Bolt\Validator
 */
class ContentTypeConstraintLoader extends AbstractLoader
{
    //
    // parseNodes() is lifted from YamlFileLoader.php
    //

    /**
     * Parses a collection of YAML nodes.
     *
     * @param array $nodes The YAML nodes
     *
     * @return array An array of values or Constraint instances
     */
    public function parseNodes(array $nodes)
    {
        $values = [];

        foreach ($nodes as $name => $childNodes) {
            if (is_numeric($name) && \is_array($childNodes) && 1 === \count($childNodes)) {
                $options = current($childNodes);

                if (\is_array($options)) {
                    $options = $this->parseNodes($options);
                }

                $values[] = $this->newConstraint(key($childNodes), $options);
            } else {
                if (\is_array($childNodes)) {
                    $childNodes = $this->parseNodes($childNodes);
                }

                $values[$name] = $childNodes;
            }
        }

        return $values;
    }

    /**
     * Will throw an Error, we only extend AbstractLoader to re-use newConstraint(), not to be used as
     * an actual loader discoverable by the validator system.
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        throw new \Error('not implemented');
    }
}
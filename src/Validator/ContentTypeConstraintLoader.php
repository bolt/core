<?php

declare(strict_types=1);

namespace Bolt\Validator;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AbstractLoader;

class ContentTypeConstraintLoader extends AbstractLoader
{
    // parseNodes() is lifted from YamlFileLoader.php

    /**
     * Parses a collection of YAML nodes.
     *
     * @param array $nodes The YAML nodes
     *
     * @return array An array of values or Constraint instances
     */
    public function parseNodes(array $nodes): array
    {
        $values = [];

        foreach ($nodes as $name => $childNodes) {
            if (is_numeric($name) && \is_array($childNodes) && \count($childNodes) === 1) {
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
    public function loadClassMetadata(ClassMetadata $metadata): bool
    {
        throw new \Error('not implemented');
    }
}

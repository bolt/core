<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FileField extends Field implements FieldInterface
{
    use FileExtrasTrait;

    public const TYPE = 'file';

    private function getFieldBase()
    {
        return [
            'filename' => '',
            'path' => '',
            'fieldname' => '',
            'url' => '',
        ];
    }

    public function __toString(): string
    {
        return $this->getPath();
    }

    public function getValue(): array
    {
        $value = array_merge($this->getFieldBase(), (array) parent::getValue() ?: []);

        // Remove cruft `0` field getting stored as JSON.
        unset($value[0]);

        $value['fieldname'] = $this->getName();

        // If the filename isn't set, we're done: return the array with placeholders
        if (! $value['filename']) {
            return $value;
        }

        // Generate a URL
        $value['path'] = $this->getPath();
        $value['url'] = $this->getUrl();

        return $value;
    }
}

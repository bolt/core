<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * @ORM\Entity
 */
class ImageField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'image';
    }

    public function __toString(): string
    {
        return $this->getPath();
    }

    public function getValue(): array
    {
        $value = parent::getValue() ?: [];

        // Remove cruft field getting stored as JSON.
        unset($value[0]);

        // Generate a URL
        $value['path'] = $this->getPath();

        return $value;
    }

    public function getPath(): string
    {
        $filesPackage = new PathPackage('/files/', new EmptyVersionStrategy());

        return $filesPackage->getUrl($this->get('filename'));
    }
}

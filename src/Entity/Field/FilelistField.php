<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FilelistField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'filelist';
    }

    public function getValue(): array
    {
        $files = (array) parent::getValue() ?: [];
        $result = [];
        foreach ($files as $key => $file) {
            $fileField = new FileField();
            $fileField->setName((string) $key);
            $fileField->setValue($file);
            array_push($result, $fileField->getValue());
        }
        return $result;
    }
}

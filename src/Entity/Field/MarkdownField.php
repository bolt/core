<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Utils\Markdown;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MarkdownField extends Field implements Excerptable, FieldInterface, RawPersistable
{
    public const TYPE = 'markdown';

    public function __toString(): string
    {
        $markdown = new Markdown();
        $value = $this->getValue();

        return $markdown->parse((string) reset($value));
    }

    public function getParsedValue(): string
    {
        return (string) $this;
    }
}

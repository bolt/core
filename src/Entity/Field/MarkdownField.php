<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Utils\Markdown;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MarkdownField extends Field implements Excerptable
{
    public function __toString(): string
    {
        $markdown = new Markdown();
        $value = $this->getValue();

        return $markdown->toHtml(reset($value));
    }

    public function getParsedValue(): string
    {
        return (string) $this;
    }
}

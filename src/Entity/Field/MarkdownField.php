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
        if (is_array($value)) {
            return $markdown->toHtml(reset($value));
        }

        return $markdown->toHtml($value);
    }
}

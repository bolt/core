<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Utils\Markdown;
use Doctrine\ORM\Mapping as ORM;
use Twig\Markup;

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

    /**
     * @return string|array|Markup
     */
    public function getTwigValue()
    {
        $value = (string) $this;

        if (is_string($value) && $this->getDefinition()->get('allow_html')) {
            $value = new Markup($value, 'UTF-8');
        }

        return $value;
    }
}

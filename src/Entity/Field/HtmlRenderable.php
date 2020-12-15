<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

/**
 * Field that should be rendered as safe html in Twig.
 */
interface HtmlRenderable
{
    public function __toString(): string;
}

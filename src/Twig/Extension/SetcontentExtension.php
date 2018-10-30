<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\TokenParser\SetcontentTokenParser;
use Twig\Extension\AbstractExtension;

/**
 * Setcontent functionality Twig extension.
 *
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class SetcontentExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        $parsers = [
            new SetcontentTokenParser(),
        ];

        return $parsers;
    }
}

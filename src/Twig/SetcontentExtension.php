<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Storage\Query;
use Bolt\Twig\TokenParser\SetcontentTokenParser;
use Twig\Extension\AbstractExtension;

/**
 * Setcontent functionality Twig extension.
 *
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class SetcontentExtension extends AbstractExtension
{
    public function __construct(
        private readonly Query $queryEngine
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers(): array
    {
        return [
            new SetcontentTokenParser(),
        ];
    }

    public function getQueryEngine(): Query
    {
        return $this->queryEngine;
    }
}

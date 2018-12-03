<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Storage\Query\Query;
use Bolt\Twig\TokenParser\SetcontentTokenParser;
use Twig\Extension\AbstractExtension;

/**
 * Setcontent functionality Twig extension.
 *
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class SetcontentExtension extends AbstractExtension
{
    /** @var Query */
    private $queryEngine;
    /** @var MetadataDriver */
    private $metadataDriver;
    
    public function __construct(Query $queryEngine, ?MetadataDriver $metadataDriver = null)
    {
        $this->queryEngine = $queryEngine;
        $this->metadataDriver = $metadataDriver; // still needed?
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
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

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
    /** @var Query $queryEngine */
    private $queryEngine;
    /** @var MetadataDriver $metadataDriver */
    private $metadataDriver;

    /**
     * @param Query          $queryEngine
     * @param MetadataDriver $metadataDriver
     */
    public function __construct(Query $queryEngine, MetadataDriver $metadataDriver = null)
    {
        $this->queryEngine = $queryEngine;
        $this->metadataDriver = $metadataDriver; // still needed?
    }

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

    /**
     * @return Query
     */
    public function getQueryEngine()
    {
        return $this->queryEngine;
    }
}

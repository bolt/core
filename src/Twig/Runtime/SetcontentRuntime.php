<?php

declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Bolt\Storage\Query\Query;

class SetcontentRuntime
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
     * @return Query
     */
    public function getQueryEngine()
    {
        return $this->queryEngine;
    }
}

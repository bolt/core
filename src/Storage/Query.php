<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Entity\Content;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class Query
{
    /** @var ContentQueryParser */
    protected $parser;

    /** @var array */
    protected $scopes = [];

    public function __construct(ContentQueryParser $parser)
    {
        $this->parser = $parser;
        $this->scopes = [];
    }

    public function addScope(string $name, QueryScopeInterface $scope): void
    {
        $this->scopes[$name] = $scope;
    }

    public function getScope(string $name): ?QueryScopeInterface
    {
        if (array_key_exists($name, $this->scopes)) {
            return $this->scopes[$name];
        }

        return null;
    }

    /**
     * getContent based on a 'human readable query'.
     *
     * Used by the twig command {% setcontent %} but also directly.
     * For reference refer to @see https://docs.bolt.cm/templating/content-fetching
     *
     * @return Pagerfanta|Content|null
     */
    public function getContent(string $textQuery, array $parameters = [])
    {
        $this->parser->setQuery($textQuery);
        $this->parser->setParameters($parameters);

        return $this->parser->fetch();
    }

    /**
     * @return Pagerfanta|Content|null
     */
    public function getContentByScope(string $scopeName, string $textQuery, array $parameters = [])
    {
        $scope = $this->getScope($scopeName);
        if ($scope) {
            $this->parser->setQuery($textQuery);
            $this->parser->setParameters($parameters);
            $this->parser->setScope($scope);

            return $this->parser->fetch();
        }

        return null;
    }

    /**
     * Helper to be called from Twig that is passed via a TwigRecordsView rather than the raw records.
     *
     * @param string $textQuery The base part like `pages` or `pages/1`
     * @param array $parameters Parameters like `printquery` and `paging`, but also `where` parameters taken from `... where { foo: bar } ...`
     *
     * @return Pagerfanta|Content|null
     */
    public function getContentForTwig(string $textQuery, array $parameters = [])
    {
        if (empty($textQuery)) {
            return new Pagerfanta(new ArrayAdapter([]));
        }

        return $this->getContentByScope('frontend', $textQuery, $parameters);
    }
}

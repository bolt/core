<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Bolt\Entity\Content;

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
     * For reference refer to @link https://docs.bolt.cm/templating/content-fetching
     *
     * @return QueryResultset|Content|null
     */
    public function getContent(string $textQuery, array $parameters = [])
    {
        $this->parser->setQuery($textQuery);
        $this->parser->setParameters($parameters);

        return $this->parser->fetch();
    }

    /**
     * @return QueryResultset|Content|null
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
     */
    public function getContentForTwig(string $textQuery, array $parameters = [], array $whereParameters = [])
    {
        $parameters = array_merge($parameters, $whereParameters);

        return $this->getContentByScope('frontend', $textQuery, $parameters);
    }
}

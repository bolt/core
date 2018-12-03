<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Bolt\Entity\Content;
use Bolt\Twig\TwigRecordsView;

class Query
{
    /** @var ContentQueryParser */
    protected $parser;
    /** @var array */
    protected $scopes = [];
    /** @var TwigRecordsView */
    protected $recordsView;

    /**
     * Constructor.
     */
    public function __construct(ContentQueryParser $parser, TwigRecordsView $recordsView)
    {
        $this->parser = $parser;
        $this->recordsView = $recordsView;
        $this->scopes = [];
    }

    /**
     * @param string $name
     */
    public function addScope($name, QueryScopeInterface $scope): void
    {
        $this->scopes[$name] = $scope;
    }

    /**
     * @param string $name
     */
    public function getScope($name): ?QueryScopeInterface
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
     * @return bool|QueryResultset|null
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
    public function getContentForTwig($textQuery, array $parameters = [])
    {
        // fix BC break
        if (func_num_args() === 3) {
            $whereparameters = func_get_arg(2);
            if (is_array($whereparameters) && ! empty($whereparameters)) {
                $parameters = array_merge($parameters, $whereparameters);
            }
        }

        $results = $this->getContentByScope('frontend', $textQuery, $parameters);
        if ($results instanceof QueryResultset) {
            $results = $results->get();
        }

        return $this->recordsView->createView($results);
    }
}

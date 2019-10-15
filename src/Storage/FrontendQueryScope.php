<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Configuration\Config;
use Bolt\Enum\Statuses;
use Bolt\Storage\Directive\OrderDirective;

/**
 * This class takes an overall config array as input and parses into values
 * applicable for performing select queries.
 *
 * This takes into account default ordering for ContentTypes.
 */
class FrontendQueryScope implements QueryScopeInterface
{
    /** @var Config */
    protected $config;

    /** @var array */
    protected $orderBys = [];

    /**
     * Constructor.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->parseContentTypes();
    }

    /**
     * Get the default order setting for a given content type.
     */
    public function getOrder(QueryInterface $query): string
    {
        $contentType = $query->getContentType();

        if (isset($this->orderBys[$contentType])) {
            return $this->orderBys[$contentType];
        }

        return 'id';
    }

    /**
     * Iterates over the main config and sets up what the default ordering should be.
     */
    protected function parseContentTypes(): void
    {
        $contentTypes = $this->config->get('contenttypes');

        foreach ($contentTypes as $type => $values) {
            $sort = $values['sort'] ?? '-publishedAt';
            $this->orderBys[$type] = $sort;
            if (isset($values['singular_slug'])) {
                $this->orderBys[$values['singular_slug']] = $sort;
            }
        }
    }

    public function onQueryExecute(QueryInterface $query): void
    {
        if (empty($query->getQueryBuilder()->getParameter('orderBy'))) {
            $handler = new OrderDirective();
            $handler($query, $this->getOrder($query));
        }

        // Setup status to only published unless otherwise specified
        $status = $query->getParameter('status');
        if (! $status) {
            $query->setParameter('status', Statuses::PUBLISHED);
        }
    }
}

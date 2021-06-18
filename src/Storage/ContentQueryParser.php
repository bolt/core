<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Storage\Directive\DirectiveHandler;
use Bolt\Storage\Handler\IdentifiedSelectHandler;
use Bolt\Storage\Handler\SelectQueryHandler;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 *  Handler class to convert the DSL for content queries into an
 *  object representation.
 *
 * @author Ross Riley <riley.ross@gmail.com>
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class ContentQueryParser
{
    /** @var ContentRepository */
    protected $repo;

    /** @var string */
    protected $query;

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $contentTypes = [];

    /** @var string */
    protected $operation;

    /** @var string */
    protected $identifier;

    /** @var array */
    protected $operations = ['search'];

    /** @var array */
    protected $directives = [];

    /** @var callable[] */
    protected $handlers = [];

    /** @var QueryInterface[] */
    protected $services = [];

    /** @var QueryScopeInterface */
    protected $scope;

    /** @var RequestStack */
    private $requestStack;

    /** @var Config */
    private $config;

    /** @var DirectiveHandler */
    private $directiveHandler;

    /**
     * Constructor.
     */
    public function __construct(
        RequestStack $requestStack,
        ContentRepository $repo,
        Config $config,
        DirectiveHandler $directiveHandler,
        ?QueryInterface $queryHandler = null)
    {
        $this->repo = $repo;
        $this->requestStack = $requestStack;

        if ($queryHandler !== null) {
            $this->addService('select', $queryHandler);
        }

        $this->setupDefaults();
        $this->config = $config;
        $this->directiveHandler = $directiveHandler;
    }

    /**
     * Internal method to initialise the default handlers.
     */
    protected function setupDefaults(): void
    {
        $this->addHandler('select', new SelectQueryHandler());
        $this->addHandler('namedselect', new IdentifiedSelectHandler());
    }

    /**
     * Sets the input query.
     *
     * @param string $query
     */
    public function setQuery($query): void
    {
        $this->query = $query;
    }

    /**
     * Sets the input parameters to handle.
     */
    public function setParameters(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Sets a single input parameter.
     *
     * @param string $param
     */
    public function setParameter($param, $value): void
    {
        $this->params[$param] = $value;
    }

    /**
     * Parse a query.
     */
    public function parse(): void
    {
        $this->parseContent();
        $this->parseOperation();
        $this->parseDirectives();
    }

    /**
     * Parses the content area of the query string.
     */
    protected function parseContent(): void
    {
        $contentString = strtok($this->query, '/');

        $content = [];
        $delim = '(),';
        $tok = strtok($contentString, $delim);
        while ($tok !== false) {
            $content[] = $tok;
            $tok = strtok($delim);
        }

        $this->setContentTypes($content);
    }

    /**
     * Internal method that takes the 'query' part of the input and
     * parses it into one of the various operations supported.
     *
     * A simple select operation will just contain the ContentType eg 'pages'
     * but additional operations can be triggered using the '/' separator.
     *
     * @internal
     */
    protected function parseOperation(): void
    {
        $operation = 'select';

        $queryParts = explode('/', $this->query);
        array_shift($queryParts);

        if (! count($queryParts)) {
            $this->operation = $operation;

            return;
        }

        if (in_array($queryParts[0], $this->operations, true)) {
            $operation = array_shift($queryParts);
            if (count($queryParts) && is_numeric($queryParts[0])) {
                $this->params['limit'] = array_shift($queryParts);
            }
            $this->identifier = implode(',', $queryParts);
        } else {
            $this->identifier = implode(',', $queryParts);
        }

        if (! empty($this->identifier)) {
            $operation = 'namedselect';
        }

        $this->operation = $operation;
    }

    /**
     * Directives are all of the other parameters supported by Bolt that do not
     * relate to an actual filter query. Some examples include 'printquery', 'limit',
     * 'order' or 'returnsingle'.
     *
     * All these need to parsed and taken out of the params that are sent to the query.
     */
    protected function parseDirectives(): void
    {
        // If the user doesn't pass in a limit, we'll get 20. Don't break the site by fetching _all_.
        $this->directives = ['limit' => 20];

        if (! $this->params) {
            return;
        }

        foreach ($this->params as $key => $value) {
            if ($this->directiveHandler->canHandle($key)) {
                $this->directives[$key] = $value;
                unset($this->params[$key]);
            }
        }
    }

    /**
     * This runs the callbacks attached to each directive command.
     */
    public function runDirectives(QueryInterface $query, array $skipDirective = []): void
    {
        $directives = $this->directives;

        while (! empty($directives)) {
            $key = array_key_first($directives);
            $value = $directives[$key];

            unset($directives[$key]);

            if (in_array($key, $skipDirective, true)) {
                continue;
            }

            if (! $this->directiveHandler->canHandle($key)) {
                continue;
            }

            $this->directiveHandler->handle($key, [$query, $value, &$directives]);
        }
    }

    public function setScope(QueryScopeInterface $scope): void
    {
        $this->scope = $scope;
    }

    public function runScopes(QueryInterface $query): void
    {
        if ($this->scope !== null) {
            $this->scope->onQueryExecute($query);
        }
    }

    /**
     * Gets the content repository.
     */
    public function getContentRepository(): ContentRepository
    {
        return $this->repo;
    }

    public function setContentTypes(array $contentTypes): void
    {
        // Verify if we're attempting to get valid ContentTypes
        foreach ($contentTypes as $key => $value) {
            $configCT = $this->config->getContentType($value);

            if (! $configCT) {
                $message = sprintf("Tried to get content from ContentType '%s', but no ContentType by that name/slug exists.", $value);

                throw new \Exception($message);
            }

            $contentTypes[$key] = $configCT->get('slug');
        }

        $this->contentTypes = $contentTypes;
    }

    /**
     * Returns the parsed content types.
     */
    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    /**
     * Returns the parsed operation.
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Returns the parsed identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns a directive from the parsed list.
     *
     * @param string|int|bool|null $key
     */
    public function getDirective($key)
    {
        if (array_key_exists($key, $this->directives)) {
            return $this->directives[$key];
        }

        return null;
    }

    /**
     * Sets a directive for the named key.
     *
     * @param string|int|bool $value
     */
    public function setDirective(string $key, $value): void
    {
        $this->directives[$key] = $value;
    }

    /**
     * Adds a handler AND operation for the named operation.
     */
    public function addHandler(string $operation, callable $callback): void
    {
        $this->handlers[$operation] = $callback;
        $this->addOperation($operation);
    }

    /**
     * Returns a handler for the named operation.
     */
    public function getHandler(string $operation): callable
    {
        return $this->handlers[$operation];
    }

    /**
     * Adds a service for the named operation.
     */
    public function addService(string $operation, QueryInterface $service): void
    {
        $this->services[$operation] = $service;
    }

    /**
     * Returns a service for the named operation.
     */
    public function getService(string $operation): QueryInterface
    {
        return $this->services[$operation];
    }

    /**
     * Returns the current parameters.
     */
    public function getParameters(): array
    {
        return $this->params;
    }

    /**
     * Helper method to check if parameters are set for a specific key.
     */
    public function hasParameter(string $param): bool
    {
        return array_key_exists($param, $this->params);
    }

    /**
     * Returns a single named parameter.
     */
    public function getParameter(string $param): array
    {
        return $this->params[$param];
    }

    /**
     * Runs the query and fetches the results.
     *
     * @return Pagerfanta|Content|null
     */
    public function fetch()
    {
        $this->parse();

        return call_user_func($this->handlers[$this->getOperation()], $this);
    }

    /**
     * Getter to return the currently registered operations.
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * Adds a new operation to the list supported.
     *
     * @param string $operation name of operation to parse for
     */
    public function addOperation(string $operation): void
    {
        if (! in_array($operation, $this->operations, true)) {
            $this->operations[] = $operation;
        }
    }

    /**
     * Removes an operation from the list supported.
     *
     * @param string $operation name of operation to remove
     */
    public function removeOperation(string $operation): void
    {
        if (in_array($operation, $this->operations, true)) {
            $key = array_search($operation, $this->operations, true);
            unset($this->operations[$key]);
        }
    }

    public function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}

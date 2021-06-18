<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Tightenco\Collect\Support\Collection;

/**
 * Handles directives on behalf of
 * the ContentQueryParser class.
 */
class DirectiveHandler
{
    /** @var Collection */
    private $directives;

    public function __construct(
        GetQueryDirective $getQueryDirective,
        LimitDirective $limitDirective,
        OrderDirective $orderDirective,
        PageDirective $pageDirective,
        PrintQueryDirective $printQueryDirective,
        ReturnSingleDirective $returnSingleDirective,
        ReturnMultipleDirective $returnMultipleDirective,
        LatestDirectiveHandler $latestDirectiveHandler,
        EarliestDirectiveHandler $earliestDirectiveHandler,
        RandomDirectiveHandler $randomDirectiveHandler
    ) {
        $this->directives = collect([]);

        $this->directives->put(GetQueryDirective::NAME, $getQueryDirective);
        $this->directives->put(LimitDirective::NAME, $limitDirective);
        $this->directives->put(OrderDirective::NAME, $orderDirective);
        $this->directives->put(PageDirective::NAME, $pageDirective);
        $this->directives->put(PrintQueryDirective::NAME, $printQueryDirective);
        $this->directives->put(ReturnSingleDirective::NAME, $returnSingleDirective);
        $this->directives->put(ReturnMultipleDirective::NAME, $returnMultipleDirective);
        $this->directives->put(LatestDirectiveHandler::NAME, $latestDirectiveHandler);
        $this->directives->put(EarliestDirectiveHandler::NAME, $earliestDirectiveHandler);
        $this->directives->put(RandomDirectiveHandler::NAME, $randomDirectiveHandler);
    }

    public function canHandle(string $directive): bool
    {
        return $this->directives->has($directive);
    }

    public function handle(string $directive, array $params): void
    {
        if (! $this->canHandle($directive)) {
            throw new InvalidArgumentException(sprintf('Unknown directive (%s).', $directive));
        }

        call_user_func_array($this->directives->get($directive), $params);
    }
}

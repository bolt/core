<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;
use Doctrine\ORM\Query\Parameter;

/**
 *  Directive a raw output of the generated query.
 */
class PrintQueryDirective
{
    public function __invoke(QueryInterface $query): void
    {
        $output = sprintf('<code>%s</code>', $query->getQueryBuilder()->getDQL());

        $output .= '<ul>';

        /** @var Parameter $parameter */
        foreach ($query->getQueryBuilder()->getParameters() as $parameter) {
            $output .= sprintf(
                '<li><code>%s</code>: <code>%s</code></li>',
                $parameter->getName(),
                $parameter->getValue()
            );
        }

        $output .= '</ul>';

        echo $output;
    }
}

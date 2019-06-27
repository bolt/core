<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Builder;

interface GraphBuilderInterface
{
    public function getQuery(): string;
}

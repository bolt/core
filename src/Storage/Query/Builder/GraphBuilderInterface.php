<?php

namespace Bolt\Storage\Query\Builder;

interface GraphBuilderInterface
{
    public function getQuery(): string;
}
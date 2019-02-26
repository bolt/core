<?php

namespace Bolt\Storage\Query\Conditional;

class Types
{
    public const CONTAINS = '_contains';
    public const NOT_CONTAINS = '_not_contains';
    public const IN = '_in';
    public const NOT_IN = '_not_in';
    public const NOT = '_not';
    public const LESS_THAN = '_lt';
    public const LESS_THAN_EQUAL = '_lte';
    public const GREATER_THAN = '_gt';
    public const GREATER_THAN_EQUAL = '_gte';
}
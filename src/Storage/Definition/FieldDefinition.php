<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Definition;

class FieldDefinition
{
    public const SUB_FIELDS = [
        'image' => [
            'filename', 'alt', 'path',
        ],
        'user' => [
            'id', 'displayName', 'username', 'email',
        ],
    ];

    public const CUSTOM_FIELDS = [
        'repeater', 'block',
    ];
}

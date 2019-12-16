<?php

declare(strict_types=1);

namespace Bolt\Storage\Definition;

class FieldDefinition
{
    public const SUB_FIELDS = [
        'image' => [
            'filename', 'alt', 'path',
        ],
        'imagelist' => [
            'filename', 'alt', 'path', 'media', 'thumbnail', 'fieldname', 'url',
        ],
        'user' => [
            'id', 'displayName', 'username', 'email',
        ],
    ];

    public const CUSTOM_FIELDS = [
        'repeater', 'block',
    ];
}

<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Entity\Content;

class ComposeValueHelper
{
    public static function get(Content $record, string $format = ''): string
    {
        if (empty($format)) {
            $format = '{title} (№ {id}, {status})';
        }

        return preg_replace_callback(
            '/{([a-z]+)}/i',
            function ($match) use ($record) {
                if ($match[1] === 'id') {
                    return $record->getId();
                }

                if ($match[1] === 'status') {
                    return $record->getStatus();
                }

                if ($record->hasField($match[1])) {
                    return $record->getField($match[1]);
                }

                if (array_key_exists($match[1], $record->getExtras())) {
                    return $record->getExtras()[$match[1]];
                }

                return '(unknown)';
            },
            $format
        );
    }
}

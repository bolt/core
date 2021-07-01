<?php

declare(strict_types=1);

namespace Bolt\Enum;

use Tightenco\Collect\Support\Collection;

/**
 * Class ImageTypes, see https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types
 */
class ImageTypes
{
    public const APNG = 'apng';
    public const AVIF = 'avif';
    public const GIF = 'gif';
    public const JPEG = 'jpeg';
    public const JPG = 'jpg';
    public const PNG = 'png';
    public const SVG = 'svg';
    public const TIFF = 'tiff';
    public const WEBP = 'webp';

    /**
     * @return string[]
     */
    public static function all(): array
    {
        $self = new \ReflectionClass(self::class);

        return array_values($self->getConstants());
    }

    public static function isValid(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        return (new Collection(static::all()))->containsStrict($status);
    }
}

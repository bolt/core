<?php

declare(strict_types=1);

namespace Bolt\Enum;

/**
 * Class ImageTypes, see https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types
 */
class ImageTypes extends BaseEnum
{
    public const APNG = 'apng';
    public const AVIF = 'avif';
    public const GIF = 'gif';
    public const JPEG = 'jpeg';
    public const JPG = 'jpg';
    public const png = 'png';
    public const SVG = 'svg';
    public const TIFF = 'tiff';
    public const WEBP = 'webp';
}

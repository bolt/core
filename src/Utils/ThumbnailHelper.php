<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Common\Str;
use Bolt\Configuration\Config;

class ThumbnailHelper
{
    /** @var Config */
    private $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config;
    }

    public function parameters(?int $width = null, ?int $height = null, ?string $location = null, ?string $path = null, ?string $fit = null): string
    {
        if (! $width && ! $height) {
            $width = $this->config->get('general/thumbnails/default_thumbnail/0', 320);
            $height = $this->config->get('general/thumbnails/default_thumbnail/1', 240);
        } elseif (! $width) {
            // If no width, let it be ridiculously high, so that it crops based on height
            $width = 10000;
        } elseif (! $height) {
            // If no height, let it be ridiculously high, so that it crops based on width
            $height = 10000;
        }

        $paramString = sprintf('%s×%s', $width, $height);

        if ($fit) {
            $paramString .= '×' . $fit;
        }

        if ($location && $location !== 'files') {
            $paramString .= '×location=' . $location;
        }

        if ($path) {
            $paramString .= '×path=' . $path;
        }

        return $paramString;
    }

    public function path(?string $filename = null, ?int $width = null, ?int $height = null, ?string $location = null, ?string $path = null, ?string $fit = null): string
    {
        if (! $filename) {
            return '/assets/images/placeholder.png';
        }

        $paramString = $this->parameters($width, $height, $location, $path, $fit);
        $filename = Str::ensureStartsWith($filename, '/');

        return sprintf('/thumbs/%s%s', $paramString, $filename);
    }
}

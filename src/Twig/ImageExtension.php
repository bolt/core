<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Entity\Field\ImageField;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Bolt\Utils\ThumbnailHelper;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    /** @var MediaRepository */
    private $mediaRepository;

    /** @var Notifications */
    private $notifications;

    /** @var ThumbnailHelper */
    private $thumbnailHelper;

    /** @var ContentExtension */
    private $contentExtension;

    /** @var Packages */
    private $assets;

    /** @var string */
    private $publicFolder;

    public function __construct(
        MediaRepository $mediaRepository,
        Notifications $notifications,
        ThumbnailHelper $thumbnailHelper,
        ContentExtension $contentExtension,
        Packages $assets,
        string $publicFolder,
        string $projectDir)
    {
        $this->mediaRepository = $mediaRepository;
        $this->notifications = $notifications;
        $this->thumbnailHelper = $thumbnailHelper;
        $this->contentExtension = $contentExtension;
        $this->assets = $assets;
        $this->publicFolder = $projectDir . DIRECTORY_SEPARATOR . $publicFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFilter('popup', [$this, 'popup'], $safe),
            new TwigFilter('showimage', [$this, 'showImage'], $safe),
            new TwigFilter('thumbnail', [$this, 'thumbnail'], $safe),
            new TwigFilter('media', [$this, 'getMedia']),
            new TwigFilter('svg', [$this, 'getSvg'], $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFunction('popup', [$this, 'popup'], $safe),
            new TwigFunction('showimage', [$this, 'showImage'], $safe),
            new TwigFunction('thumbnail', [$this, 'thumbnail'], $safe),
            new TwigFunction('media', [$this, 'media']),
        ];
    }

    public function popup($image, int $width = 320, int $height = 240, ?string $class = null): string
    {
        $link = $this->getFilename($image);
        $thumbnail = $this->thumbnail($image, $width, $height);
        $alt = $this->getAlt($image);

        $class = $class ? ' class="{$class}"' : '';

        return sprintf('<a href="%s" class="bolt-popup"><img src="%s" alt="%s"%s></a>', $link, $thumbnail, $alt, $class);
    }

    /**
     * @param ImageField|array|string $image
     */
    public function showImage($image, ?int $width = null, ?int $height = null, ?bool $lazy = null): string
    {
        $filename = $this->getFilename($image, true);
        $alt = $this->getAlt($image);

        $path = $this->assets->getUrl($filename, 'files');

        if ($width) {
            $width = sprintf('width="%s"', $width);
        }
        if ($height) {
            $height = sprintf('height="%s"', $height);
        }
        if ($lazy) {
            $lazy = 'loading="lazy"';
        }

        return sprintf('<img src="%s" alt="%s" %s %s %s>', $path, $alt, (string) $width, (string) $height, $lazy);
    }

    /**
     * @param ImageField|array|string $image
     */
    public function thumbnail($image, ?int $width = null, ?int $height = null, ?string $location = null, ?string $path = null, ?string $fit = null, ?int $quality = null)
    {
        $filename = $this->getFilename($image, true);

        return $this->thumbnailHelper->path($filename, $width, $height, $location, $path, $fit, $quality);
    }

    /**
     * @param ImageField|array $image
     */
    public function getMedia($image): ?Media
    {
        if (is_array($image) && array_key_exists('media', $image)) {
            return $this->mediaRepository->findOneBy(['id' => $image['media']]);
        }

        if ($image instanceof ImageField) {
            return $image->getLinkedMedia($this->mediaRepository);
        }

        return $this->notifications->warning(
            'Incorrect usage of `media`-filter',
            'The `media`-filter can only be applied to an `ImageField`, or an array that has a key named `media` which holds an id.'
        );
    }

    /**
     * @param ImageField|Content|array|string $image
     */
    public function getSvg($image): ?string
    {
        $image = $this->publicFolder . $this->assets->getUrl($this->getFilename($image, true), 'files');
        $extension = pathinfo($image, PATHINFO_EXTENSION);

        if ($extension !== 'svg') {
            return null;
        }

        if (! file_exists($image)) {
            return null;
        }

        return file_get_contents($image);
    }

    /**
     * @param ImageField|Content|array|string $image
     */
    private function getFilename($image, bool $relative = false): ?string
    {
        $filename = null;

        if ($image instanceof Content) {
            $image = $this->contentExtension->getImage($image);
        }

        if (is_array($image)) {
            $filename = $image['filename'];
        } elseif ($relative && ($image instanceof ImageField)) {
            $filename = $image->get('filename');
        } else {
            $filename = (string) $image;
        }

        return $filename;
    }

    /**
     * @param ImageField|Content|array|string $image
     */
    private function getAlt($image): string
    {
        $alt = '';

        if ($image instanceof Content) {
            $image = $this->contentExtension->getImage($image);
        }

        if ($image instanceof ImageField) {
            $alt = $image->get('alt');
        } elseif (is_array($image)) {
            $alt = $image['alt'];
        } elseif (is_string($image)) {
            $alt = $image;
        }

        return htmlentities((string) $alt, ENT_QUOTES);
    }
}

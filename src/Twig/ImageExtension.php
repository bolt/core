<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field\ImageField;
use League\Glide\Urls\UrlBuilderFactory;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    /** @var Config */
    private $config;

    private $secret = '';

    /** @var Packages */
    private $packages;

    public function __construct(Config $config, Packages $packages)
    {
        $this->config = $config;
        $this->secret = $this->config->get('general/secret');
        $this->packages = $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        // Note: we do _not_ include 'image' here, because it would clash with the
        // magic "Image" extras.
        return [
            new TwigFilter('popup', [$this, 'popup'], ['is_safe' => ['html']]),
            new TwigFilter('showimage', [$this, 'showImage'], ['is_safe' => ['html']]),
            new TwigFilter('thumbnail', [$this, 'thumbnail'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('image', [$this, 'image'], ['is_safe' => ['html']]),
            new TwigFunction('popup', [$this, 'popup'], ['is_safe' => ['html']]),
            new TwigFunction('showimage', [$this, 'showImage'], ['is_safe' => ['html']]),
            new TwigFunction('thumbnail', [$this, 'thumbnail'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param ImageField|array|string $image
     */
    public function image($image): string
    {
        $filename = $this->getFilename($image);

        return $this->packages->getUrl($filename, 'files');
    }

    public function popup($image, int $width = 320, int $height = 240): string
    {
        $link = $this->image($image);
        $thumbnail = $this->thumbnail($image, $width, $height);
        $alt = $this->getAlt($image);

        return sprintf('<a href="%s" class="bolt-popup"><img src="%s" alt="%s"></a>', $link, $thumbnail, $alt);
    }

    /**
     * @param ImageField|array|string $image
     */
    public function showImage($image, ?int $width = null, ?int $height = null): string
    {
        $link = $this->image($image);
        $alt = $this->getAlt($image);

        if ($width) {
            $width = sprintf('width="%s"', $width);
        }
        if ($height) {
            $height = sprintf('height="%s"', $height);
        }

        return sprintf('<img src="%s" alt="%s" %s %s>', $link, $alt, (string) $width, (string) $height);
    }

    /**
     * @param ImageField|array|string $image
     */
    public function thumbnail($image, int $width = 320, int $height = 240, ?string $location = null, ?string $path = null, ?string $fit = null)
    {
        $filename = $this->getFilename($image);

        if (empty($filename)) {
            return '';
        }

        $params = [
            'w' => $width,
            'h' => $height,
        ];

        if ($location) {
            $params['location'] = $location;
        }
        if ($path) {
            $params['path'] = $path;
        }
        if ($fit) {
            $params['fit'] = $fit;
        }

        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create('/thumbs/', $this->secret);

        // Generate a URL
        return $urlBuilder->getUrl($filename, $params);
    }

    /**
     * @param ImageField|Content|array|string $image
     */
    private function getFilename($image): ?string
    {
        $filename = null;

        if ($image instanceof Content) {
            $image = $this->getImageFromContent($image);
        }

        if ($image instanceof ImageField) {
            $filename = $image->get('filename');
        } elseif (is_array($image)) {
            $filename = $image['filename'];
        } elseif (is_string($image)) {
            $filename = $image;
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
            $image = $this->getImageFromContent($image);
        }

        if ($image instanceof ImageField) {
            $alt = $image->get('alt');
        } elseif (is_array($image)) {
            $alt = $image['alt'];
        } elseif (is_string($image)) {
            $alt = $image;
        }

        return htmlentities($alt, ENT_QUOTES);
    }

    private function getImageFromContent(Content $content): ?ImageField
    {
        foreach ($content->getFields() as $field) {
            if ($field instanceof ImageField) {
                return $field;
            }
        }

        return null;
    }
}

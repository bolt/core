<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Field;
use League\Glide\Urls\UrlBuilderFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    private $secret = '';

    private $config = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->secret = $this->config->get('general/secret');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('showimage', [$this, 'dummy']),
            new TwigFilter('thumbnail', [$this, 'thumbnail'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('image', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('thumbnail', [$this, 'thumbnail'], ['is_safe' => ['html']]),
            new TwigFunction('popup', [$this, 'dummy'], ['is_safe' => ['html']]),
        ];
    }

    public function thumbnail($image, $width = 320, $height = 240, $area = null, $path = null)
    {
        if ($image instanceof Field) {
            $filename = $image->get('filename');
        } elseif (is_string($image)) {
            $filename = $image;
        }

        if (empty($filename)) {
            return '';
        }

        $params = [
            'w' => $width,
            'h' => $height,
        ];

        if ($area) {
            $params['area'] = $area;
        }
        if ($path) {
            $params['path'] = $path;
        }

        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create('/thumbs/', $this->secret);

        // Generate a URL
        return $urlBuilder->getUrl($filename, $params);
    }

    public function dummy($input = null)
    {
        return $input;
    }
}

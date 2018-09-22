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
    private $key;

    private $config;

    public function __construct(Config $config)
    {
        $this->key = 'foo';
        $this->config = $config;
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

    public function thumbnail($image, $width, $height)
    {
        if ($image instanceof Field) {
            $filename = $image->get('filename');
        } elseif (is_string($image)) {
            $filename = $image;
        }

        $secret = $this->config->get('general/secret');

        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create('/thumbs/', $secret);

        // Generate a URL
        $url = $urlBuilder->getUrl($filename, ['w' => 500]);

        return $url;
    }

    public function dummy($input = null)
    {
        return $input;
    }
}

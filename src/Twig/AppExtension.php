<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFilter('order', [$this, 'dummy']),
            new TwigFilter('unique', [$this, 'unique']),
            new TwigFilter('localedatetime', [$this, 'localedatetime'], $safe),
            new TwigFilter('showimage', [$this, 'dummy']),
            new TwigFilter('ucwords', [$this, 'ucwords']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('image', [$this, 'dummy'], $safe),
            new TwigFunction('thumbnail', [$this, 'dummy'], $safe),
            new TwigFunction('widgets', [$this, 'dummy'], $safe),
            new TwigFunction('popup', [$this, 'dummy'], $safe),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }

    public function ucwords($content, string $delimiters = ''): string
    {
        if (! $content) {
            return '';
        }

        return ucwords($content, $delimiters);
    }

    public function unique($array): array
    {
        return array_unique($array);
    }

    public function localedatetime($dateTime, $format = '%B %e, %Y %H:%M', $locale = 0)
    {
        if (! $dateTime instanceof \DateTime) {
            $dateTime = new \DateTime($dateTime);
        }

        // Check for Windows to find and replace the %e modifier correctly
        // @see: http://php.net/strftime
        $os = mb_strtoupper(mb_substr(PHP_OS, 0, 3));
        $format = $os !== 'WIN' ? $format : preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);

        // According to http://php.net/manual/en/function.setlocale.php manual
        // if the second parameter is "0", the locale setting is not affected,
        // only the current setting is returned.
        $result = setlocale(LC_ALL, $locale);

        if ($result === false) {
            // This shouldn't occur, but.. Dude!
            // You ain't even got locale or English on your platform??
            // Various things we could do. We could fail miserably, but a more
            // graceful approach is to use the datetime to display a default
            // format
            // $this->systemLogger->error('No valid locale detected. Fallback on DateTime active.', ['event' => 'system']);

            return $dateTime->format('Y-m-d H:i:s');
        }
        $timestamp = $dateTime->getTimestamp();

        return strftime($format, $timestamp);
    }
}

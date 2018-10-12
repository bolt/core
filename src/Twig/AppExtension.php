<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Helpers\Excerpt;
use Bolt\Utils\Markdown;
use Symfony\Component\Intl\Intl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $parser;
    private $localeCodes;
    private $locales;
    private $menuBuilder;

    public function __construct(Markdown $parser, string $locales)
    {
        $this->parser = $parser;
        $this->localeCodes = explode('|', $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
            new TwigFilter('markdown', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
            new TwigFilter('order', [$this, 'dummy']),
            new TwigFilter('unique', [$this, 'unique']),
            new TwigFilter('localedatetime', [$this, 'dummy']),
            new TwigFilter('showimage', [$this, 'dummy']),
            new TwigFilter('excerpt', [$this, 'excerpt']),
            new TwigFilter('ucwords', [$this, 'ucwords']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', [$this, 'getLocales']),
            new TwigFunction('__', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('image', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('thumbnail', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('widgets', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('htmllang', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('popup', [$this, 'dummy'], ['is_safe' => ['html']]),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }

    public function ucwords($content, string $delimiters = ''): string
    {
        if (!$content) {
            return '';
        }

        return ucwords($content, $delimiters);
    }

    public function unique($array): array
    {
        return array_unique($array);
    }

    /**
     * Transforms the given Markdown content into HTML content.
     */
    public function markdownToHtml(string $content): string
    {
        return $this->parser->toHtml($content);
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, Español, etc.).
     */
    public function getLocales(): array
    {
        if (null !== $this->locales) {
            return $this->locales;
        }

        $this->locales = [];
        foreach ($this->localeCodes as $localeCode) {
            $this->locales[] = ['code' => $localeCode, 'name' => Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode)];
        }

        return $this->locales;
    }

    public function excerpt($text, $length = 100)
    {
        $excerpter = new Excerpt($text);

        return $excerpter->getExcerpt((int) $length);
    }
}

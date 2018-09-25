<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Content\MenuBuilder;
use Bolt\Utils\Markdown;
use Symfony\Component\Intl\Intl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This Twig extension adds a new 'md2html' filter to easily transform Markdown
 * contents into HTML contents inside Twig templates.
 *
 * See https://symfony.com/doc/current/cookbook/templating/twig_extension.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Julien ITARD <julienitard@gmail.com>
 */
class AppExtension extends AbstractExtension
{
    private $parser;
    private $localeCodes;
    private $locales;
    private $menuBuilder;

    public function __construct(Markdown $parser, string $locales, MenuBuilder $menuBuilder)
    {
        $this->parser = $parser;
        $this->localeCodes = explode('|', $locales);
        $this->menuBuilder = $menuBuilder;
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
            new TwigFilter('localedatetime', [$this, 'dummy']),
            new TwigFilter('showimage', [$this, 'dummy']),
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
            new TwigFunction('sidebarmenu', [$this, 'sidebarmenu']),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
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

    public function sidebarmenu()
    {
        $menu = $this->menuBuilder->get();

        dump($menu);

        return $menu;
    }
}

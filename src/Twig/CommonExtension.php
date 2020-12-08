<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CommonExtension extends AbstractExtension
{
    /** @var ContentExtension */
    private $contentExtension;
    /** @var FrontendMenuExtension */
    private $frontendMenuExtension;

    /** @var LocaleExtension */
    private $localeExtension;

    public function __construct(ContentExtension $contentExtension, FrontendMenuExtension $frontendMenuExtension, LocaleExtension $localeExtension)
    {
        $this->contentExtension = $contentExtension;
        $this->frontendMenuExtension = $frontendMenuExtension;
        $this->localeExtension = $localeExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('current', [$this, 'isCurrent'], $env),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('secret', [$this, 'generateSecret']),
        ];
    }

    public function isCurrent(Environment $env, $item): bool
    {
        if ($item instanceof Content) {
            return $this->contentExtension->isCurrent($env, $item);
        } elseif (is_iterable($item) && array_key_exists('uri', $item)) {
            return $this->frontendMenuExtension->isCurrent($item);
        } elseif ($this->getLocale($item)) {
            return $this->localeExtension->getHtmlLang($env) === $this->getLocale($item);
        }

        return false;
    }

    public function generateSecret(string $slug): string
    {
        return md5(getenv('APP_SECRET') . $slug);
    }

    private function getLocale($item): ?string
    {
        if (is_string($item)) {
            $localepattern = '/^[a-z]{2}((-|_)[a-z]{2})?$/m';
            preg_match_all($localepattern, $item, $matches);

            return ! empty($matches) ? $item : null;
        } elseif ($item instanceof Collection) {
            return $this->getLocale($item->get('code', null));
        }

        return null;
    }
}

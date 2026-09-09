<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Illuminate\Support\Collection;
use RuntimeException;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CommonExtension extends AbstractExtension
{
    public function __construct(
        private readonly ContentExtension $contentExtension,
        private readonly FrontendMenuExtension $frontendMenuExtension,
        private readonly LocaleExtension $localeExtension,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('current', $this->isCurrent(...), $env),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('secret', $this->generateSecret(...)),
        ];
    }

    public function isCurrent(Environment $env, $item): bool
    {
        if ($item instanceof Content) {
            return $this->contentExtension->isCurrent($env, $item);
        }

        if (is_iterable($item) && array_key_exists('uri', $item)) {
            return $this->frontendMenuExtension->isCurrent($item);
        }

        if ($this->getLocale($item)) {
            return $this->localeExtension->getHtmlLang($env) === $this->getLocale($item);
        }

        return false;
    }

    public function generateSecret(string $slug): string
    {
        $secret = $_ENV['APP_SECRET'] ?? null;

        if (empty($secret) && getenv('APP_SECRET')) {
            $secret = getenv('APP_SECRET');
        }

        if (! $secret) {
            throw new RuntimeException('App secret not set');
        }

        return md5($secret . $slug);
    }

    private function getLocale($item): ?string
    {
        if (is_string($item)) {
            return preg_match('/^[a-z]{2}((-|_)[a-z]{2})?$/m', $item) === 1 ? $item : null;
        }

        if ($item instanceof Collection) {
            return $this->getLocale($item->get('code', null));
        }

        return null;
    }
}

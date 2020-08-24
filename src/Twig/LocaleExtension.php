<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Utils\LocaleHelper;
use Carbon\Carbon;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LocaleExtension extends AbstractExtension
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var LocaleHelper */
    private $localeHelper;

    /** @var Config */
    private $config;

    /** @var string */
    private $defaultLocale;

    /** @var Environment */
    private $twig;

    public function __construct(TranslatorInterface $translator, LocaleHelper $localeHelper, Config $config, Environment $twig, string $defaultLocale)
    {
        $this->translator = $translator;
        $this->localeHelper = $localeHelper;
        $this->config = $config;
        $this->defaultLocale = $defaultLocale;
        $this->twig = $twig;
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
            new TwigFilter('localedatetime', [$this, 'localedatetime'], $safe),
            new TwigFilter('localedate', [$this, 'localedatetime'], $safe),
            new TwigFilter('localdate', [$this, 'localdate'], $safe),
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
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('__', [$this, 'translate'], $safe),
            new TwigFunction('htmllang', [$this, 'getHtmlLang'], $env),
            new TwigFunction('locales', [$this, 'getLocales'], $env),
            new TwigFunction('locale', [$this, 'getLocale']),
            new TwigFunction('flag', [$this, 'flag'], $safe),
        ];
    }

    public function getHtmlLang(Environment $twig): string
    {
        $current = $this->localeHelper->getCurrentLocale($twig);

        if ($current) {
            return $current->get('code');
        }

        return '';
    }

    public function translate(string $id, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string|Collection $localeCode
     */
    public function getLocale($localeCode): Collection
    {
        return $this->localeHelper->localeInfo($localeCode);
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, Español, etc.).
     */
    public function getLocales(Environment $twig, ?Collection $localeCodes = null, bool $all = false): Collection
    {
        return $this->localeHelper->getLocales($twig, $localeCodes, $all);
    }

    /**
     * @param string|Collection $localeCode
     */
    public function flag($localeCode): string
    {
        $locale = $this->localeHelper->localeInfo($localeCode);

        return sprintf(
            '<span class="fp mr-1 %s" title="%s - %s / %s"></span>',
            $locale->get('flag'),
            $locale->get('name'),
            $locale->get('localizedname'),
            $locale->get('code')
        );
    }

    /**
     * @deprecated
     *
     * @param string|\DateTime $dateTime
     */
    public function localedatetime($dateTime, string $format = '%B %e, %Y %H:%M', ?string $locale = '0'): string
    {
        if (! $dateTime instanceof \DateTime) {
            $dateTime = new \DateTime((string) $dateTime);
        }

        // Check for Windows to find and replace the %e modifier correctly
        // @see: http://php.net/strftime
        $os = mb_strtoupper(mb_substr(PHP_OS, 0, 3));
        $format = $os !== 'WIN' ? $format : preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        $timestamp = $dateTime->getTimestamp();

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

        return strftime($format, $timestamp);
    }

    public function localdate($dateTime, ?string $format = null, ?string $locale = null): string
    {
        if ($dateTime instanceof \Datetime) {
            $dateTime = Carbon::createFromTimestamp($dateTime->getTimestamp(), $dateTime->getTimezone());
        } elseif (empty($dateTime)) {
            $dateTime = Carbon::now();
        } else {
            $dateTime = Carbon::createFromTimeString($dateTime);
        }

        if ($format === null) {
            $format = $this->config->get('general/date_format');
        }

        if ($locale === null) {
            $current = $this->getHtmlLang($this->twig);
            $locale = ! empty($current) ? $current : $this->defaultLocale;
        }

        $dateTime->locale($locale);

        return $dateTime->translatedFormat($format);
    }
}

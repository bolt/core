<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field\Excerptable;
use Bolt\Twig\LocaleExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentHelper
{
    /** @var Canonical */
    private $canonical;

    /** @var Request */
    private $request;

    /** @var Config */
    private $config;

    /** @var LocaleExtension */
    private $localeExtension;

    public function __construct(Canonical $canonical, RequestStack $requestStack, Config $config, LocaleExtension $localeExtension)
    {
        $this->canonical = $canonical;
        $this->request = $requestStack->getCurrentRequest() ?? Request::createFromGlobals();
        $this->config = $config;
        $this->localeExtension = $localeExtension;
    }

    public function setCanonicalPath($record, ?string $locale = null): void
    {
        if (! $record instanceof Content) {
            return;
        }

        $route = $this->getCanonicalRouteAndParams($record, $locale);

        $this->canonical->setPath($route['route'], $route['params']);
    }

    public function getLink($record, bool $canonical = false, ?string $locale = null): ?string
    {
        if (! $record instanceof Content) {
            return '';
        }

        $route = $this->getCanonicalRouteAndParams($record, $locale);

        // Clone the canonical, as it is a shared service.
        // We only want to get the url for the current request
        $canonicalObj = clone $this->canonical;
        $canonicalObj->setPath($route['route'], $route['params']);

        return $canonical ? $canonicalObj->get() : $canonicalObj->getPath();
    }

    private function getCanonicalRouteAndParams(Content $record, ?string $locale = null): array
    {
        if ($this->isHomepage($record)) {
            return [
                'route' => 'homepage_locale',
                'params' => [
                    '_locale' => $locale,
                ],
            ];
        }

        if (! $locale) {
            $locale = $this->request->getLocale();
        }

        return [
            'route' => $record->getDefinition()->get('record_route'),
            'params' => [
                'contentTypeSlug' => $record->getContentTypeSingularSlug(),
                'slugOrId' => $record->getSlug($locale),
                '_locale' => $locale,
            ],
        ];
    }

    public function isHomepage(Content $content): bool
    {
        return $this->isSpecialpage($content, 'homepage');
    }

    public function is404(Content $content): bool
    {
        return $this->isSpecialpage($content, 'notfound');
    }

    public function is403(Content $content): bool
    {
        return $this->isSpecialpage($content, 'forbidden');
    }

    public function isMaintenance(Content $content): bool
    {
        return $this->isSpecialpage($content, 'maintenance');
    }

    public static function isSuitable(Content $record, string $which = 'title_format'): bool
    {
        $definition = $record->getDefinition();

        if ($record->getId() && $definition !== null && $definition->has($which)) {
            $format = $definition->get($which);
            if (is_string($format) && mb_strpos($format, '{') !== false) {
                return true;
            }
        }

        return false;
    }

    public function get(Content $record, string $format = '', ?string $locale = null): string
    {
        if (empty($format)) {
            $format = '{title} (№ {id}, {status})';
        }

        if (empty($locale) && $record->hasContentTypeLocales()) {
            $locale = $record->getContentTypeDefaultLocale();
        }

        return preg_replace_callback(
            '/{([\w]+)}/i',
            function ($match) use ($record, $locale) {
                if ($match[1] === 'id') {
                    return $record->getId();
                }

                if ($match[1] === 'status') {
                    return $record->getStatus();
                }

                if ($match[1] === 'author') {
                    return $record->getAuthor();
                }

                if ($match[1] === 'publishedAt') {
                    return $this->localeExtension->localdate($record->getPublishedAt(), null, $locale);
                }

                if ($match[1] === 'modifiedAt') {
                    return $this->localeExtension->localdate($record->getModifiedAt(), null, $locale);
                }

                if ($match[1] === 'createdAt') {
                    return $this->localeExtension->localdate($record->getCreatedAt(), null, $locale);
                }

                if ($match[1] === 'depublishedAt') {
                    return $this->localeExtension->localdate($record->getDepublishedAt(), null, $locale);
                }

                if ($match[1] === 'contenttype') {
                    return $record->getDefinition()['singular_name'];
                }

                if ($record->hasField($match[1])) {
                    $field = $record->getField($match[1]);

                    if ($field->isTranslatable()) {
                        $field->setLocale($locale);
                    }

                    return $field;
                }

                if ($record->hasTaxonomyDefined($match[1])) {
                    $result = [];
                    foreach ($record->getTaxonomies($match[1]) as $item) {
                        $result[] = $item->getName();
                    }

                    return implode(', ', $result);
                }

                // We must ensure this method is not called recursively. For example
                // `title_format: {title}` would otherwise result in an endless loop
                // @see https://github.com/bolt/core/issues/1825
                if (Recursion::detect()) {
                    return '(recursion)';
                }

                if (array_key_exists($match[1], $record->getExtras())) {
                    // If it's the icon, return an html element
                    if ($match[1] === 'icon') {
                        return sprintf("<i class='fas %s'></i>", $record->getExtras()[$match[1]]);
                    }

                    return $record->getExtras()[$match[1]];
                }

                return '(unknown)';
            },
            $format
        );
    }

    public static function getFieldNames(string $format): array
    {
        preg_match_all('/{([\w]+)}/i', $format, $matches);

        return $matches[1];
    }

    public static function guessTitleFields(Content $content): array
    {
        // Check if we have a field named 'title' or somesuch.
        // English
        $names = ['title', 'name', 'caption', 'subject'];
        // Dutch
        $names = array_merge($names, ['titel', 'naam', 'kop', 'onderwerp']);
        // French
        $names = array_merge($names, ['nom', 'sujet']);
        // Spanish
        $names = array_merge($names, ['nombre', 'sujeto']);

        foreach ($names as $name) {
            if ($content->hasField($name)) {
                return (array) $name;
            }
        }

        foreach ($content->getFields() as $field) {
            if ($field instanceof Excerptable) {
                return (array) $field->getName();
            }
        }

        return [];
    }

    public static function getFieldBasedTitle(Content $content, string $locale = ''): string
    {
        $titleParts = [];

        foreach (self::guessTitleFields($content) as $fieldName) {
            $field = $content->getField($fieldName);

            if (! empty($locale)) {
                $field->setCurrentLocale($locale);
            }

            $value = $field->getParsedValue();

            if (empty($value)) {
                $value = $field->setLocale($field->getDefaultLocale())->getParsedValue();
            }

            $titleParts[] = $value;
        }

        return implode(' ', $titleParts);
    }

    private function isSpecialpage(Content $content, string $type): bool
    {
        $configSetting = $this->config->get('general/' . $type);

        if (! is_iterable($configSetting)) {
            $configSetting = (array) $configSetting;
        }

        foreach ($configSetting as $item) {
            $item = explode('/', $item);

            // Discard candidate if contentTypes don't match
            if ($item[0] !== $content->getContentTypeSingularSlug() && $item[0] !== $content->getContentTypeSlug()) {
                continue;
            }

            $idOrSlug = $item[1] ?? null;

            // Success if we either have no id/slug for a Singleton, or if the id/slug matches
            if ((empty($idOrSlug) && $content->getDefinition()->get('singleton')) ||
                ($idOrSlug === $content->getSlug() || $idOrSlug === (string) $content->getId())) {
                return true;
            }
        }

        return false;
    }
}

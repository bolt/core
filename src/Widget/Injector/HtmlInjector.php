<?php

declare(strict_types=1);

namespace Bolt\Widget\Injector;

use Bolt\Common\Str;
use Bolt\Widget\WidgetInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class for matching HTML elements and injecting text.
 */
class HtmlInjector
{
    /**
     * Get a map of function names to locations. We don't have unique callbacks
     * for all targets, because in practice they end up in about the same
     * location
     */
    public function getMap(): array
    {
        return [
            Target::END_OF_HEAD => 'headTagEnd',
            // same as end of head
            Target::AFTER_HEAD_JS => 'headTagEnd',
            // same as end of head
            Target::AFTER_HEAD_CSS => 'headTagEnd',
            // same as end of head because meta tags are unordered
            Target::AFTER_HEAD_META => 'headTagEnd',

            Target::BEFORE_CSS => 'cssTagsBefore',
            Target::BEFORE_JS => 'jsTagsBefore',
            Target::AFTER_META => 'metaTagsAfter',
            Target::AFTER_CSS => 'cssTagsAfter',
            Target::AFTER_JS => 'jsTagsAfter',

            Target::START_OF_HEAD => 'headTagStart',
            // same as start of head
            Target::BEFORE_HEAD_JS => 'headTagStart',
            // same as start of head
            Target::BEFORE_HEAD_CSS => 'headTagStart',
            // same as start of head because meta tags are unordered
            Target::BEFORE_HEAD_META => 'headTagStart',

            Target::START_OF_BODY => 'bodyTagStart',
            // same as start of body
            Target::BEFORE_BODY_JS => 'bodyTagStart',
            // same as start of body
            Target::BEFORE_BODY_CSS => 'bodyTagStart',

            Target::END_OF_BODY => 'bodyTagEnd',
            // same as end of body
            Target::AFTER_BODY_JS => 'bodyTagEnd',
            // same as end of body
            Target::AFTER_BODY_CSS => 'bodyTagEnd',

            Target::END_OF_HTML => 'htmlTagEnd',

            Target::BEFORE_CONTENT => 'beforeContent',
            Target::AFTER_CONTENT => 'afterContent',

            Target::NOWHERE => 'nowhere',
        ];
    }

    public function inject(WidgetInterface $widget, Response $response): void
    {
        $targets = $widget->getTargets();
        $functionMap = $this->getMap();

        // Targets that this class knows how to handle.
        $targets = array_intersect($targets, array_keys($functionMap));

        if (empty($targets)) {
            return;
        }

        // We have at least one target to process. Invoke the (snippet)Widget
        $snippet = $widget() . "\n";

        // If the widget doesn't produce output, there's no need to inject it.
        if (mb_strlen($snippet) === 0) {
            return;
        }

        foreach ($targets as $target) {
            $this->injectSnippet($snippet, $target, $functionMap, $response);
        }
    }

    private function injectSnippet(string $snippet, string $target, array $functionMap, Response $response): void
    {
        $html = $this->{$functionMap[$target]}($snippet, $response->getContent());
        $response->setContent($html);
    }

    /**
     * insert some HTML into the very beginning of HTML / Content.
     */
    protected function beforeContent(string $snippet, string $rawHtml): string
    {
        return $snippet . $rawHtml;
    }

    /**
     * insert some HTML into the very end of HTML / Content.
     */
    protected function afterContent(string $snippet, string $rawHtml): string
    {
        return $rawHtml . $snippet;
    }

    /**
     * Nowhere, don't replace anything.
     */
    public static function nowhere(string $snippet, string $rawHtml): string
    {
        return $rawHtml;
    }

    /**
     * insert some HTML into the start of the head section of
     * an HTML page, right after the <head> tag.
     */
    protected function headTagStart(string $snippet, string $rawHtml): string
    {
        return self::injectAfterTagStart($rawHtml, 'head', $snippet);
    }

    /**
     * insert some HTML into the head section of an HTML
     * page, right before the </head> tag.
     */
    protected function headTagEnd(string $snippet, string $rawHtml): string
    {
        return self::injectBeforeTagEnd($rawHtml, 'head', $snippet);
    }

    /**
     * insert some HTML into the start of the head section of
     * an HTML page, right after the <body> tag.
     */
    protected function bodyTagStart(string $snippet, string $rawHtml): string
    {
        return self::injectAfterTagStart($rawHtml, 'body', $snippet);
    }

    /**
     * insert some HTML into the body section of an HTML
     * page, right before the </body> tag.
     */
    protected function bodyTagEnd(string $snippet, string $rawHtml): string
    {
        return self::injectBeforeTagEnd($rawHtml, 'body', $snippet);
    }

    /**
     * insert some HTML into the html section of an HTML
     * page, right before the </html> tag.
     */
    protected function htmlTagEnd(string $snippet, string $rawHtml): string
    {
        return self::injectBeforeTagEnd($rawHtml, 'html', $snippet);
    }

    /**
     * insert some HTML into the head section of an HTML page.
     * If there is no Metatag anywhere, place it right before end of head.
     */
    protected function metaTagsAfter(string $snippet, string $rawHtml): string
    {
        $result = self::injectAfterTagEnd($rawHtml, 'meta', $snippet);
        if ($result === $rawHtml) {
            $result = self::injectBeforeTagEnd($rawHtml, 'head', $snippet);
        }

        return $result;
    }

    /**
     * insert some HTML into the head section of an HTML page.
     * If there is no CSS anywhere, place it right before end of head.
     */
    protected function cssTagsAfter(string $snippet, string $rawHtml): string
    {
        $result = self::injectAfterTagEnd($rawHtml, 'link', $snippet);
        if ($result === $rawHtml) {
            $result = self::injectAfterTagEnd($rawHtml, 'style', $snippet);
        }
        if ($result === $rawHtml) {
            $result = self::injectBeforeTagEnd($rawHtml, 'head', $snippet);
        }

        return $result;
    }

    /**
     * insert some HTML before the first CSS include in the page.
     * If there is no CSS anywhere, place it right after start of head.
     */
    protected function cssTagsBefore(string $snippet, string $rawHtml): string
    {
        $result = self::injectBeforeTagStart($rawHtml, 'link', $snippet);
        if ($result === $rawHtml) {
            $result = self::injectBeforeTagStart($rawHtml, 'style', $snippet);
        }
        if ($result === $rawHtml) {
            $result = self::injectAfterTagStart($rawHtml, 'head', $snippet);
        }

        return $result;
    }

    /**
     * insert some HTML before the first javascript include in the page.
     * If there is no JS anywhere, place it right after start of body.
     */
    protected function jsTagsBefore(string $snippet, string $rawHtml): string
    {
        $result = self::injectBeforeTagStart($rawHtml, 'script', $snippet);
        if ($result === $rawHtml) {
            $result = self::injectAfterTagStart($rawHtml, 'body', $snippet);
        }

        return $result;
    }

    /**
     * insert some HTML after the last javascript include.
     * If there is no JS anywhere, place it right before end of body.
     */
    protected function jsTagsAfter(string $snippet, string $rawHtml): string
    {
        $result = self::injectAfterTagEnd($rawHtml, 'script', $snippet);
        if ($result === $rawHtml) {
            $result = self::injectBeforeTagEnd($rawHtml, 'body', $snippet);
        }

        return $result;
    }

    protected static function findTagStart(string $rawHtml, string $htmlTag): ?string
    {
        preg_match('~(<' . $htmlTag . '[^>]*?>)~mi', $rawHtml, $matches);

        if (empty($matches)) {
            return null;
        }

        return $matches[1];
    }

    protected static function findTagEnd(string $rawHtml, string $htmlTag): ?string
    {
        preg_match_all('~((<' . $htmlTag . '(\s[^>]*)?>)|(</' . $htmlTag . '>))~mi', $rawHtml, $allMatches);

        if (empty($allMatches)) {
            return null;
        }
        foreach (array_reverse($allMatches[0]) as $match) {
            if ($match !== '') {
                return $match;
            }
        }

        return null;
    }

    public static function injectBeforeTagStart(string $rawHtml, string $htmlTag, string $injection): string
    {
        $match = static::findTagStart($rawHtml, $htmlTag);
        if ($match === null) {
            return static::nowhere($injection, $rawHtml);
        }

        return Str::replaceFirst($rawHtml, $match, $injection . $match, true);
    }

    public static function injectAfterTagStart(string $rawHtml, string $htmlTag, string $injection): string
    {
        $match = static::findTagStart($rawHtml, $htmlTag);
        if ($match === null) {
            return static::nowhere($injection, $rawHtml);
        }

        return Str::replaceFirst($rawHtml, $match, $match . $injection, true);
    }

    public static function injectBeforeTagEnd(string $rawHtml, string $htmlTag, string $injection): string
    {
        $match = static::findTagEnd($rawHtml, $htmlTag);
        if ($match === null) {
            return static::nowhere($injection, $rawHtml);
        }

        return Str::replaceLast($rawHtml, $match, $injection . $match, true);
    }

    public static function injectAfterTagEnd(string $rawHtml, string $htmlTag, string $injection): string
    {
        $match = static::findTagEnd($rawHtml, $htmlTag);
        if ($match === null) {
            return static::nowhere($injection, $rawHtml);
        }

        return Str::replaceLast($rawHtml, $match, $match . $injection, true);
    }
}

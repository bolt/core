<?php

namespace Bolt\Snippet;

use Bolt\Common\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class for matching HTML elements and injecting text.
 *
 * @author Bob den Otter <bob@twokings.nl>
 */
class Injector
{
    /**
     * Get a map of function names to locations. We don't have unique callbacks 
     * for all targets, because in practice they end up in about the same 
     * location
     */
    public function getMap(): array 
    {
        return [
            Target::END_OF_HEAD      => 'headTagEnd',
            Target::AFTER_HEAD_JS    => 'headTagEnd', // same as end of head 
            Target::AFTER_HEAD_CSS   => 'headTagEnd', // same as end of head 
            Target::AFTER_HEAD_META  => 'headTagEnd', // same as end of head because meta tags are unordered

            Target::BEFORE_CSS       => 'cssTagsBefore',
            Target::BEFORE_JS        => 'jsTagsBefore',
            Target::AFTER_META       => 'metaTagsAfter',
            Target::AFTER_CSS        => 'cssTagsAfter',
            Target::AFTER_JS         => 'jsTagsAfter',

            Target::START_OF_HEAD    => 'headTagStart',
            Target::BEFORE_HEAD_JS   => 'headTagStart', // same as start of head 
            Target::BEFORE_HEAD_CSS  => 'headTagStart', // same as start of head 
            Target::BEFORE_HEAD_META => 'headTagStart', // same as start of head because meta tags are unordered

            Target::START_OF_BODY    => 'bodyTagStart',
            Target::BEFORE_BODY_JS   => 'bodyTagStart', // same as start of body 
            Target::BEFORE_BODY_CSS  => 'bodyTagStart', // same as start of body 

            Target::END_OF_BODY      => 'bodyTagEnd',
            Target::AFTER_BODY_JS    => 'bodyTagEnd',   // same as end of body 
            Target::AFTER_BODY_CSS   => 'bodyTagEnd',   // same as end of body 

            Target::END_OF_HTML      => 'htmlTagEnd',
            Target::AFTER_HTML       => 'htmlTagEnd',
        ];
    }

    public function inject(string $snippet, string $location, Response $response): void 
    {
        $html = $response->getContent();
        $functionMap = $this->getMap();
        if (isset($functionMap[$location])) {
            $html = $this->{$functionMap[$location]}($snippet, $html);
        } else {
            $html .= "$snippet\n";
        }

        $response->setContent($html);
    }

    /**
     * Helper function to insert some HTML into the start of the head section of
     * an HTML page, right after the <head> tag.
     */
    protected function headTagStart(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '<head', true, false)) {
            $replacement = sprintf("%s\n%s\t%s", $matches[0], $matches[1], (string) $asset);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML into the head section of an HTML
     * page, right before the </head> tag.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function headTagEnd(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '</head', false, false)) {
            $replacement = sprintf("%s\t%s\n%s", $matches[1], (string) $asset, $matches[0]);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML into the start of the head section of
     * an HTML page, right after the <body> tag.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function bodyTagStart(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '<body', true, false)) {
            $replacement = sprintf("%s\n%s\t%s", $matches[0], $matches[1], (string) $asset);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML into the body section of an HTML
     * page, right before the </body> tag.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function bodyTagEnd(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '</body', false, false)) {
            $replacement = sprintf("%s\t%s\n%s", $matches[1], $asset, $matches[0]);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML into the html section of an HTML
     * page, right before the </html> tag.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function htmlTagEnd(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '</html', false, false)) {
            $replacement = sprintf("%s\t%s\n%s", $matches[1], $asset, $matches[0]);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML into the head section of an HTML page.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function metaTagsAfter(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '<meta', true, true)) {
            $last = count($matches[0]) - 1;
            $replacement = sprintf("%s\n%s%s", $matches[0][$last], $matches[1][$last], (string) $asset);

            return Str::replaceFirst($rawHtml, $matches[0][$last], $replacement);
        }

        return $this->headTagEnd($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML into the head section of an HTML page.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function cssTagsAfter(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '<link', true, true)) {
            $last = count($matches[0]) - 1;
            $replacement = sprintf("%s\n%s%s", $matches[0][$last], $matches[1][$last], (string) $asset);

            return Str::replaceFirst($rawHtml, $matches[0][$last], $replacement);
        }

        return $this->headTagEnd($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML before the first CSS include in the page.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function cssTagsBefore(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '<link', true, false)) {
            $replacement = sprintf("%s%s\n%s\t%s", $matches[1], $asset, $matches[0], $matches[1]);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML before the first javascript include in the page.
     *
     * @param string $asset
     * @param string         $rawHtml
     *
     * @return string
     */
    protected function jsTagsBefore(string $asset, string $rawHtml): string
    {
        if ($matches = $this->getMatches($rawHtml, '<script', true, false)) {
            $replacement = sprintf("%s%s\n%s\t%s", $matches[1], $asset, $matches[0], $matches[1]);

            return Str::replaceFirst($rawHtml, $matches[0], $replacement);
        }

        return $this->tagSoup($asset, $rawHtml);
    }

    /**
     * Helper function to insert some HTML after the last javascript include.
     * First in the head section, but if there is no script in the head, place
     * it anywhere.
     */
    protected function jsTagsAfter(string $asset, string $rawHtml, $insidehead = true): string
    {
        if ($insidehead) {
            $pos = strpos($rawHtml, '</head>');
            $context = substr($rawHtml, 0, $pos);
        } else {
            $context = $rawHtml;
        }

        // This match tag is a unique case
        if ($matches = $this->getMatches($context, '(.*)</script>', false, true)) {
            // Attempt to insert it after the last <script> tag within context, matching indentation.
            $last = count($matches[0]) - 1;
            $replacement = sprintf("%s\n%s%s", $matches[0][$last], $matches[1][$last], (string) $asset);

            return Str::replaceFirst($rawHtml, $matches[0][$last], $replacement);
        } elseif ($insidehead) {
            // Second attempt: entire document
            return $this->jsTagsAfter($asset, $rawHtml, false);
        }

        return $this->headTagEnd($asset, $rawHtml);
    }

    /**
     * Get a set of matches.
     *
     * @param string $rawHtml        The original HTML
     * @param string $htmlTag        HTML tag fragment we're matching, e.g. '<head' or '</head'
     * @param bool   $matchRemainder TRUE matches the remainder of the line, not just the tag - (.*)
     * @param bool   $matchAll       TRUE returns all matched instances - preg_match_all()
     *
     * @return string[]|false
     */
    private function getMatches(string $rawHtml, string $htmlTag, bool $matchRemainder, bool $matchAll): ?array
    {
        $matches = null;
        $matchRemainder = $matchRemainder ? '(.*)' : '';
        $regex = sprintf("~^([ \t]*)%s%s~mi", $htmlTag, $matchRemainder);

        if ($matchAll && preg_match_all($regex, $rawHtml, $matches)) {
            return $matches;
        } elseif (!$matchAll && preg_match($regex, $rawHtml, $matches)) {
            return $matches;
        }

        return false;
    }

    /**
     * Since we're serving tag soup, just append the tag to the HTML we're given.
     */
    private function tagSoup(string $asset, string $rawHtml): string
    {
        return $rawHtml . (string) $asset . "\n";
    }
}

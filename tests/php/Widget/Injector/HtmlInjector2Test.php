<?php

declare(strict_types=1);

namespace Bolt\Tests\Widget\Injector;

use Bolt\Tests\StringTestCase;
use Bolt\Widget\Injector\HtmlInjector;
use PHPUnit\Framework\Attributes\DataProvider;

class HtmlInjector2Test extends StringTestCase
{
    public const HTML = '<html><body class="something"
    >foo<p><p
    class="inner">bar</p></p><script></script><script
     /></body></html>';

    public static function providerInjectBeforeTagStart(): array
    {
        return [
            [
                'body',
                '<html>koala<body class="something">foo<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
            [
                'script',
                '<html><body class="something">foo<p><p class="inner">bar</p></p>koala<script></script><script /></body></html>',
            ],
            [
                'p',
                '<html><body class="something">fookoala<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
            [
                'nope',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
        ];
    }

    public static function providerInjectBeforeTagEnd(): array
    {
        return [
            [
                'body',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script />koala</body></html>',
            ],
            [
                'script',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script>koala<script /></body></html>',
            ],
            [
                'p',
                '<html><body class="something">foo<p><p class="inner">bar</p>koala</p><script></script><script /></body></html>',
            ],
            [
                'nope',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
        ];
    }

    public static function providerInjectAfterTagStart(): array
    {
        return [
            [
                'body',
                '<html><body class="something">koalafoo<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
            [
                'script',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script>koala</script><script /></body></html>',
            ],
            [
                'p',
                '<html><body class="something">foo<p>koala<p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
            [
                'nope',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
        ];
    }

    public static function providerInjectAfterTagEnd(): array
    {
        return [
            [
                'body',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script /></body>koala</html>',
            ],
            [
                'script',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script />koala</body></html>',
            ],
            [
                'p',
                '<html><body class="something">foo<p><p class="inner">bar</p></p>koala<script></script><script /></body></html>',
            ],
            [
                'nope',
                '<html><body class="something">foo<p><p class="inner">bar</p></p><script></script><script /></body></html>',
            ],
        ];
    }

    #[DataProvider('providerInjectBeforeTagStart')]
    public function testInjectBeforeTagStart(string $tag, string $expected): void
    {
        $result = HtmlInjector::injectBeforeTagStart(
            self::HTML,
            $tag,
            'koala'
        );
        self::assertSameHtml(
            $expected,
            $result
        );
    }

    #[DataProvider('providerInjectBeforeTagEnd')]
    public function testInjectBeforeTagEnd(string $tag, string $expected): void
    {
        $result = HtmlInjector::injectBeforeTagEnd(
            self::HTML,
            $tag,
            'koala'
        );
        self::assertSameHtml(
            $expected,
            $result
        );
    }

    #[DataProvider('providerInjectAfterTagStart')]
    public function testInjectAfterTagStart(string $tag, string $expected): void
    {
        $result = HtmlInjector::injectAfterTagStart(
            self::HTML,
            $tag,
            'koala'
        );
        self::assertSameHtml(
            $expected,
            $result
        );
    }

    #[DataProvider('providerInjectAfterTagEnd')]
    public function testInjectAfterTagEnd(string $tag, string $expected): void
    {
        $result = HtmlInjector::injectAfterTagEnd(
            self::HTML,
            $tag,
            'koala'
        );
        self::assertSameHtml(
            $expected,
            $result
        );
    }

    public function testInjectAfterLinkEnd(): void
    {
        $tag = 'link';
        $expected = '<html><head>bar<link src="foo"></head><link src="bar" /><body>foo<link src="baz"></link></body><link src="end">koalabaz</html>';
        $html = '<html><head>bar<link src="foo"></head><link src="bar" /><body>foo<link src="baz"></link></body><link src="end">baz</html>';

        $result = HtmlInjector::injectAfterTagEnd(
            $html,
            $tag,
            'koala'
        );
        self::assertSameHtml(
            $expected,
            $result
        );
    }
}

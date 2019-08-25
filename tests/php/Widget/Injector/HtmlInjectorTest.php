<?php

declare(strict_types=1);

namespace Bolt\Tests\Widget\Injector;

use Bolt\Tests\StringTestCase;
use Bolt\Widget\Injector\HtmlInjector;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\SnippetWidget;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;

class HtmlInjectorTest extends StringTestCase
{
    private const TEST_TEMPLATES_BASE_PATH = __DIR__ . '/../../../fixtures/HtmlInjector/';

    public function providerTarget()
    {
        $list = (new Target())->listAll();
        $constants = (new Collection(array_keys($list)))
            ->filter(function ($v) {
                return mb_strpos($v, 'NOWHERE') === false
                    && mb_strpos($v, 'BEFORE_HTML') === false
                    && mb_strpos($v, 'AFTER_HTML') === false
                    && mb_strpos($v, 'BEFORE_CONTENT') === false
                    && mb_strpos($v, 'AFTER_CONTENT') === false;
            })
            ->map(function ($v) {
                return [$v];
            });

        return $constants->toArray();
    }

    public function providerAlwaysWorkingTarget()
    {
        $list = (new Target())->listAll();
        $constants = (new Collection(array_keys($list)))
            ->filter(function ($v) {
                return mb_strpos($v, 'BEFORE_HTML') !== false
                || mb_strpos($v, 'AFTER_HTML') !== false
                || mb_strpos($v, 'BEFORE_CONTENT') !== false
                || mb_strpos($v, 'AFTER_CONTENT') !== false;
            })
            ->map(function ($v) {
                return [$v];
            });

        return $constants->toArray();
    }

    /**
     * @dataProvider providerTarget
     */
    public function testMap(string $constant): void
    {
        $constant = constant('Bolt\Widget\Injector\Target::' . $constant);
        $injector = new HtmlInjector();

        self::assertArrayHasKey($constant, $injector->getMap());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInject(string $constant): void
    {
        $expected = file_get_contents(self::TEST_TEMPLATES_BASE_PATH . 'result.' . $constant . '.html');
        $constant = constant('Bolt\Widget\Injector\Target::' . $constant);
        $injector = new HtmlInjector();

        $snippet = new SnippetWidget('koala', '', $constant);

        $response = new Response($this->getHtml());
        $injector->inject($snippet, $response);

        self::assertSameHtml($expected, $response->getContent());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInjectNoLinebreaks(string $constant): void
    {
        $expected = file_get_contents(self::TEST_TEMPLATES_BASE_PATH . 'result.' . $constant . '.html');
        $constant = constant('Bolt\Widget\Injector\Target::' . $constant);
        $injector = new HtmlInjector();

        $snippet = new SnippetWidget('koala', '', $constant);

        $response = new Response(preg_replace('/(\r|\n)+/', '', $this->getHtml()));
        $injector->inject($snippet, $response);

        self::assertSameHtml($expected, $response->getContent());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInjectInvalidLocation(string $constant): void
    {
        $injector = new HtmlInjector();

        $snippet = new SnippetWidget('koala', '', $constant);

        $html = $this->getHtml();
        $response = new Response($html);
        $injector->inject($snippet, $response);

        self::assertSameHtml($html, $response->getContent());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInjectEmptyHtml(string $constant): void
    {
        $constant = constant('Bolt\Widget\Injector\Target::' . $constant);
        $injector = new HtmlInjector();

        $snippet = new SnippetWidget('koala', '', $constant);

        $response = new Response();
        $injector->inject($snippet, $response);

        self::assertSameHtml('', $response->getContent());
    }

    /**
     * @dataProvider providerAlwaysWorkingTarget
     */
    public function testInjectEmptyHtmlAlwaysWorking(string $constant): void
    {
        $constant = constant('Bolt\Widget\Injector\Target::' . $constant);
        $injector = new HtmlInjector();

        $snippet = new SnippetWidget('koala', '', $constant);

        $response = new Response();
        $injector->inject($snippet, $response);

        self::assertSameHtml('koala', $response->getContent());
    }

    protected function getHtml()
    {
        return file_get_contents(self::TEST_TEMPLATES_BASE_PATH . 'index.html');
    }
}

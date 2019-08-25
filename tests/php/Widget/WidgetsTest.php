<?php

declare(strict_types=1);

namespace Bolt\Tests\Widget;

use BobdenOtter\WeatherWidget\WeatherWidget;
use Bolt\Tests\StringTestCase;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\Injector\HtmlInjector;
use Bolt\Widget\Injector\QueueProcessor;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\SnippetWidget;
use Bolt\Widgets;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\Cache\Simple\Psr6Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class WidgetsTest extends StringTestCase
{
    private function getWidgetsObject(array $templates = ['weather.twig' => '[Hello, weather!]'], $zone = RequestZone::BACKEND): Widgets
    {
        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();

        $request = Request::createFromGlobals();
        RequestZone::setToRequest($request, $zone);
        $requestStack->push($request);

        $loader = new ArrayLoader($templates);
        $twig = new Environment($loader);

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));
        $stopwatch = new Stopwatch();

        return new Widgets($requestStack, $queueProcessor, $twig, $cache, $stopwatch);
    }

    public function testProcessWidgetsInQueue(): void
    {
        $widgets = $this->getWidgetsObject();

        $response = new Response('<html><body>foo</body></html>');

        $snippet = (new SnippetWidget())
            ->setTemplate('*foo*')
            ->setZone(RequestZone::EVERYWHERE)
            ->setTarget(Target::END_OF_BODY);

        $widgets->registerWidget($snippet);
        $widgets->processQueue($response);

        $this->assertSameHtml('<html><body>foo*foo*</body></html>', $response->getContent());
    }

    public function testRenderWidget(): void
    {
        $widgets = $this->getWidgetsObject();

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $widgets->registerWidget($weatherWidget);

        $this->assertSameHtml(
            '<div class="widget" id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div>',
            $widgets->renderWidgetByName('Weather Widget')
        );
    }

    public function testRenderWidgetWithExtraParameters(): void
    {
        $widgets = $this->getWidgetsObject(['dummy.twig' => '[Hello, {{ foo }}!]']);

        $widget = new DummyWidget();
        $widget->setTemplate('dummy.twig');

        $widgets->registerWidget($widget);

        $this->assertSameHtml(
            '<div class="widget" id="widget-dummy-widget" name="Dummy Widget">[Hello, Bar!]</div>',
            $widgets->renderWidgetByName('Dummy Widget', ['foo' => 'Bar'])
        );
    }

    public function testProcessHeaderWidget(): void
    {
        $widgets = $this->getWidgetsObject([], RequestZone::FRONTEND);

        $response = new Response('<html><body>foo</body></html>');

        $headerWidget = new BoltHeaderWidget();

        $widgets->registerWidget($headerWidget);
        $widgets->processQueue($response);

        $this->assertSameHtml('Bolt', $response->headers->get('X-Powered-By'));
    }

    public function testProcessWeatherWidgetInTarget(): void
    {
        $widgets = $this->getWidgetsObject();

        $response = new Response('<html><body>foo</body></html>');

        $weather = new WeatherWidget();

        // overwrite things just to simplify test
        $weather->setTarget(Target::END_OF_BODY);
        $weather->setTemplate('weather.twig');

        $widgets->registerWidget($weather);
        $widgets->processQueue($response);

        $this->assertSameHtml(
            '<html><body>foo<div class="widget" id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div></body></html>',
            $response->getContent()
        );
    }

    public function testProcessWeatherWidgetInTarget2(): void
    {
        $widgets = $this->getWidgetsObject();

        $response = new Response('<html><body>foo</body></html>');

        $weather = new WeatherWidget();

        // overwrite things just to simplify test
        $weather->setTarget(Target::START_OF_BODY);
        $weather->setTemplate('weather.twig');

        $widgets->registerWidget($weather);
        $widgets->processQueue($response);

        $this->assertSameHtml(
            '<html><body><div class="widget" id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div>foo</body></html>',
            $response->getContent()
        );
    }
}

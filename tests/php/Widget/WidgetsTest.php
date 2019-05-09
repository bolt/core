<?php

declare(strict_types=1);

namespace Bolt\Tests\Widget;

use Bolt\Tests\StringTestCase;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\Injector\HtmlInjector;
use Bolt\Widget\Injector\QueueProcessor;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\SnippetWidget;
use Bolt\Widget\WeatherWidget;
use Bolt\Widgets;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\Cache\Simple\Psr6Cache;
use Twig\Loader\ArrayLoader;

class WidgetsTest extends StringTestCase
{
    public function testProcessWidgetsInQueue(): void
    {
        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();

        $request = Request::createFromGlobals();
        RequestZone::setToRequest($request, RequestZone::BACKEND);
        $requestStack->push($request);

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));

        $widgets = new Widgets($requestStack, $queueProcessor, $twig, $cache);
        $response = new Response('<html><body>foo</body></html>');

        $snippet = (new SnippetWidget())
            ->setTemplate('*foo*')
            ->setZone(RequestZone::EVERYWHERE)
            ->setTarget(Target::END_OF_BODY);

        $widgets->registerWidget($snippet);
        $widgets->processQueue($response);

        $this->assertSameHtml("<html><body>foo*foo*</body></html>", $response->getContent());
    }

    public function testRenderWidget(): void
    {
        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));

        $widgets = new Widgets($requestStack, $queueProcessor, $twig, $cache);

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $widgets->registerWidget($weatherWidget);

        $this->assertSameHtml(
            '<div id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div>',
            $widgets->renderWidgetByName('Weather Widget')
        );
    }


    public function testRenderWidgetWithExtraParameters(): void
    {
        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, {{ foo }}!]']);
        $twig = new Environment($loader);

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));

        $widgets = new Widgets($requestStack, $queueProcessor, $twig, $cache);

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $widgets->registerWidget($weatherWidget);

        $this->assertSameHtml(
            '<div id="widget-weather-widget" name="Weather Widget">[Hello, Bar!]</div>',
            $widgets->renderWidgetByName('Weather Widget', ['foo' => 'Bar'])
        );
    }

    public function testProcessHeaderWidget(): void
    {
        $request = new Request();
        RequestZone::setToRequest($request, RequestZone::FRONTEND);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $twig = new Environment(new ArrayLoader());

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));

        $widgets = new Widgets($requestStack, $queueProcessor, $twig, $cache);

        $response = new Response('<html><body>foo</body></html>');

        $headerWidget = new BoltHeaderWidget();

        $widgets->registerWidget($headerWidget);
        $widgets->processQueue($response);

        $this->assertSameHtml('Bolt', $response->headers->get('X-Powered-By'));
    }

    public function testProcessWeatherWidgetInTarget(): void
    {
        $request = new Request();
        RequestZone::setToRequest($request, RequestZone::BACKEND);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));

        $widgets = new Widgets($requestStack, $queueProcessor, $twig, $cache);

        $response = new Response('<html><body>foo</body></html>');

        $weather = new WeatherWidget();

        // overwrite things just to simplify test
        $weather->setTarget(Target::END_OF_BODY);
        $weather->setTemplate('weather.twig');

        $widgets->registerWidget($weather);
        $widgets->processQueue($response);

        $this->assertSameHtml(
            '<html><body>foo<div id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div></body></html>',
            $response->getContent()
        );
    }

    public function testProcessWeatherWidgetInTarget2(): void
    {
        $request = new Request();
        RequestZone::setToRequest($request, RequestZone::BACKEND);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));

        $widgets = new Widgets($requestStack, $queueProcessor, $twig, $cache);

        $response = new Response('<html><body>foo</body></html>');

        $weather = new WeatherWidget();

        // overwrite things just to simplify test
        $weather->setTarget(Target::START_OF_BODY);
        $weather->setTemplate('weather.twig');

        $widgets->registerWidget($weather);
        $widgets->processQueue($response);

        $this->assertSameHtml(
            '<html><body><div id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div>foo</body></html>',
            $response->getContent()
        );
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Tests;

use Bolt\Widget\Injector\HtmlInjector;
use Bolt\Widget\Injector\QueueProcessor;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\SnippetWidget;
use Bolt\Widget\WeatherWidget;
use Bolt\Widgets;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class WidgetsTest extends TestCase
{
    public function testProcessWidgetsInQueue(): void
    {
        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $widgets = new Widgets($requestStack, $queueProcessor, $twig);
        $response = new Response('<html><body>foo</body></html>');

        $snippet = (new SnippetWidget())
            ->setTemplate('*foo*')
            ->setTarget(Target::END_OF_BODY);

        $widgets->registerWidget($snippet);
        $widgets->processQueue($response);

        $this->assertSame("<html><body>foo</body></html>*foo*\n", $response->getContent());
    }

    public function testRenderWidget(): void
    {
        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $widgets = new Widgets($requestStack, $queueProcessor, $twig);

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $widgets->registerWidget($weatherWidget);

        $this->assertSame(
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

        $widgets = new Widgets($requestStack, $queueProcessor, $twig);

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $widgets->registerWidget($weatherWidget);

        $this->assertSame(
            '<div id="widget-weather-widget" name="Weather Widget">[Hello, Bar!]</div>',
            $widgets->renderWidgetByName('Weather Widget', ['foo' => 'Bar'])
        );
    }

    public function testProcessHeaderWidget(): void
    {
        $request = new Request();
        $request->attributes->set(RequestZone::KEY, RequestZone::FRONTEND);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $twig = new Environment(new ArrayLoader());

        $widgets = new Widgets($requestStack, $queueProcessor, $twig);

        $response = new Response('<html><body>foo</body></html>');

        $headerWidget = new BoltHeaderWidget();

        $widgets->registerWidget($headerWidget);
        $widgets->processQueue($response);

        $this->assertSame('Bolt', $response->headers->get('X-Powered-By'));
    }

    public function testProcessWeatherWidget(): void
    {
        $request = new Request();
        $request->attributes->set(RequestZone::KEY, RequestZone::BACKEND);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queueProcessor = new QueueProcessor(new HtmlInjector());
        $twig = new Environment(new ArrayLoader());

        $widgets = new Widgets($requestStack, $queueProcessor, $twig);

        $response = new Response('<html><body>foo</body></html>');

        $weatherWidget = new WeatherWidget();

        $widgets->registerWidget($weather);
        $widgets->processQueue($response);

        $this->assertContains('Bolt', $response->headers->get('X-Powered-By'));
    }
}

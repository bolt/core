<?php

declare(strict_types=1);

namespace Bolt\Tests;

use Bolt\Snippet\HtmlInjector;
use Bolt\Snippet\QueueProcessor;
use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;
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
        $queueprocessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $widgets = new Widgets($requestStack, $queueprocessor, $twig);
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
        $queueprocessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $widgets = new Widgets($requestStack, $queueprocessor, $twig);

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
        $queueprocessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, {{ foo }}!]']);
        $twig = new Environment($loader);

        $widgets = new Widgets($requestStack, $queueprocessor, $twig);

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $widgets->registerWidget($weatherWidget);

        $this->assertSame(
            '<div id="widget-weather-widget" name="Weather Widget">[Hello, Bar!]</div>',
            $widgets->renderWidgetByName('Weather Widget', null, ['foo' => 'Bar'])
        );
    }

    public function testProcessHeaderWidget(): void
    {
        $queueprocessor = new QueueProcessor(new HtmlInjector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $widgets = new Widgets($requestStack, $queueprocessor, $twig);

        $response = new Response('<html><body>foo</body></html>');

        $headerWidget = new BoltHeaderWidget();
        $headerWidget->setZone(RequestZone::NOWHERE);

        $widgets->registerWidget($headerWidget);
        $widgets->processQueue($response);

        $this->assertSame('Bolt', $response->headers->get('X-Powered-By'));
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Tests;

use Bolt\Snippet\Injector;
use Bolt\Snippet\QueueProcessor;
use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;
use Bolt\Snippets;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\WeatherWidget;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class SnippetsTest extends TestCase
{
    public function testSnippet(): void
    {
        $queueprocessor = new QueueProcessor(new Injector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $snippets = new Snippets($requestStack, $queueprocessor, $twig);
        $response = new Response('<html><body>foo</body></html>');

        $snippets->registerSnippet('*foo*', Target::END_OF_BODY, Zone::NOWHERE, 'test');
        $snippets->processQueue($response);

        $this->assertSame("<html><body>foo</body></html>*foo*\n", $response->getContent());
    }

    public function testWidget(): void
    {
        $queueprocessor = new QueueProcessor(new Injector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $snippets = new Snippets($requestStack, $queueprocessor, $twig);

        $weatherWidget = new WeatherWidget();
        $weatherWidget->setTemplate('weather.twig');

        $snippets->registerWidget($weatherWidget);

        $this->assertSame(
            '<div id="widget-weather-widget" name="Weather Widget">[Hello, weather!]</div>',
            $snippets->renderWidgetByName('Weather Widget')
        );
    }

    public function testProcessHeaderWidget(): void
    {
        $queueprocessor = new QueueProcessor(new Injector());
        $requestStack = new RequestStack();
        $requestStack->push(Request::createFromGlobals());

        $loader = new ArrayLoader(['weather.twig' => '[Hello, weather!]']);
        $twig = new Environment($loader);

        $snippets = new Snippets($requestStack, $queueprocessor, $twig);

        $response = new Response('<html><body>foo</body></html>');

        $headerWidget = new BoltHeaderWidget();
        $headerWidget->setZone(Zone::NOWHERE);

        $snippets->registerWidget($headerWidget);
        $snippets->processQueue($response);

        $this->assertSame('Bolt', $response->headers->get('X-Powered-By'));
    }
}

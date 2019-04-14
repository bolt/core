<?php

declare(strict_types=1);

namespace Bolt\Tests\Asset;

use Bolt\Snippet\Injector;
use Bolt\Snippet\Target;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;

class InjectorTest extends TestCase
{
    public function providerTarget()
    {
        $constants = (new Collection(array_keys((new Target)->listAll())))
            ->filter(function ($v) {
                return mb_strpos($v, 'WIDGET') === false && mb_strpos($v, 'NOWHERE') === false;
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
        $constant = constant('Bolt\Snippet\Target::' . $constant);
        $injector = new Injector();

        self::assertArrayHasKey($constant, $injector->getMap());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInject(string $constant): void
    {
        $expected = file_get_contents(__DIR__ . '/../../fixtures/Injector/result.' . $constant . '.html');
        $constant = constant('Bolt\Snippet\Target::' . $constant);
        $injector = new Injector();

        $snippet = [
            'callback' => 'koala',
            'target' => $constant,
        ];

        $response = new Response($this->getHtml());
        $injector->inject($snippet, $response);

        self::assertSame($expected, $response->getContent());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInjectInvalidLocation(string $constant): void
    {
        $constant = constant('Bolt\Snippet\Target::' . $constant);
        $injector = new Injector();

        $snippet = [
            'callback' => 'koala',
            'target' => '',
        ];

        $html = $this->getHtml();
        $response = new Response($html);
        $injector->inject($snippet, $response);

        self::assertSame($html . "koala\n", $response->getContent());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInjectEmptyHtml(string $constant): void
    {
        $constant = constant('Bolt\Snippet\Target::' . $constant);
        $injector = new Injector();

        $snippet = [
            'callback' => 'koala',
            'target' => $constant,
        ];

        $response = new Response();
        $injector->inject($snippet, $response);

        self::assertSame("koala\n", $response->getContent());
    }

    /**
     * @dataProvider providerTarget
     */
    public function testInjectTagSoup(string $constant): void
    {
        $constant = constant('Bolt\Snippet\Target::' . $constant);
        $injector = new Injector();

        $snippet = [
            'callback' => 'koala',
            'target' => $constant,
        ];

        $response = new Response('<blink>');
        $injector->inject($snippet, $response);

        self::assertSame("<blink>koala\n", $response->getContent());
    }

    protected function getHtml()
    {
        return file_get_contents(__DIR__ . '/../../fixtures/Injector/index.html');
    }
}

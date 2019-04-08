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
        $o = new \ReflectionClass(Target::class);
        $constants = (new Collection(array_keys($o->getConstants())))
            ->filter(function ($v) {
                return (mb_strpos($v, 'WIDGET') === false && mb_strpos($v, 'NOWHERE') === false);
            })
            ->map(function ($v) {
                return [$v];
            });

        return $constants->toArray();
    }

    /**
     * @dataProvider providerTarget
     *
     * @param string $constant
     */
    public function testMap($constant): void
    {
        $constant = constant('Bolt\Snippet\Target::' . $constant);
        $injector = new Injector();

        self::assertArrayHasKey($constant, $injector->getMap());
    }

    /**
     * @dataProvider providerTarget
     *
     * @param string $constant
     */
    public function testInject($constant): void
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
     *
     * @param string $constant
     */
    public function testInjectInvalidLocation($constant): void
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
     *
     * @param string $constant
     */
    public function testInjectEmptyHtml($constant): void
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
     *
     * @param string $constant
     */
    public function testInjectTagSoup($constant): void
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

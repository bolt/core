<?php

declare(strict_types=1);

namespace Bolt\Tests\Widget\Injector;

use Bolt\Widget\Injector\RequestZone;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tightenco\Collect\Support\Collection;

class RequestZoneTest extends TestCase
{
    public function providerZone()
    {
        $o = new \ReflectionClass(RequestZone::class);
        $constants = (new Collection(array_keys($o->getConstants())))
            ->filter(function ($v) {
                return mb_strpos($v, 'NOWHERE') === false;
            })
            ->map(function ($v) {
                return [$v];
            });

        return $constants->toArray();
    }

    public function testGetDefaultZone(): void
    {
        $request = Request::createFromGlobals();

        $this->assertSame('nowhere', RequestZone::getFromRequest($request));
    }

    /**
     * @dataProvider providerZone
     */
    public function testZone(string $constant): void
    {
        $request = Request::createFromGlobals();

        RequestZone::setToRequest($request, $constant);

        $this->assertSame($constant, RequestZone::getFromRequest($request));
        $this->assertTrue(RequestZone::is($request, $constant));
    }
}

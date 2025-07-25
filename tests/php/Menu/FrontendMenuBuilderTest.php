<?php

declare(strict_types=1);

namespace Bolt\Tests\Menu;

use RuntimeException;
use Bolt\Collection\DeepCollection;
use Bolt\Menu\FrontendMenuBuilder;
use Bolt\Tests\DbAwareTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class FrontendMenuBuilderTest extends DbAwareTestCase
{
    /** @var FrontendMenuBuilder */
    private $menuBuilder;

    /** @var MockObject */
    private $twig;

    /** @var MockObject */
    private $request;

    /** @var MockObject */
    private $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menuBuilder = self::getContainer()->get(FrontendMenuBuilder::class);

        // Setup mocks for testing the localized menu
        $this->twig = $this->createMock(Environment::class);
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->method('getLocale')->wilLReturn('en');

        $this->request->attributes = $this->createMock(ParameterBag::class);
        $this->request->attributes
            ->expects($matcher = $this->atMost(2))
            ->method('get')
            ->willReturnCallback(function (string $route) use ($matcher) {
                if ($matcher->getInvocationCount() === 1) {
                    $this->assertSame('_route', $route);
                    return 'homepage_locale';
                }

                if ($matcher->getInvocationCount() === 2) {
                    $this->assertSame('_route_params', $route);
                    return [];
                }

                throw new RuntimeException('Unexpected call');
            });
        $this->app = $this->createMock(AppVariable::class);
        $this->app->method('getRequest')->willReturn($this->request);
        $this->twig->method('getGlobals')->willReturn(['app' => $this->app]);
    }

    public function testNonExistingMenu(): void
    {
        $this->expectException(RuntimeException::class);
        $this->menuBuilder->buildMenu($this->twig, 'foo');
    }

    public function testDefaultMenuIsFirst(): void
    {
        $menu1 = $this->menuBuilder->buildMenu($this->twig);
        $menu2 = $this->menuBuilder->buildMenu($this->twig, 'main');

        $this->assertSame($menu1, $menu2);
    }

    public function testFirstItem(): void
    {
        $menu = $this->menuBuilder->buildMenu($this->twig, 'main');

        $firstItem = DeepCollection::deepMake($menu)->first();

        $this->assertSame('Home', $firstItem->get('label'));
        $this->assertSame('This is the <b>first<b> menu item.', $firstItem->get('title'));
        $this->assertSame('homepage', $firstItem->get('link'));
        $this->assertSame('homepage', $firstItem->get('class'));
        $this->assertNull($firstItem->get('submenu'));
        $this->assertSame('/', $firstItem->get('uri'));
        $this->assertNull($firstItem->get('foobar'));
    }

    public function testLastItem(): void
    {
        $menu = $this->menuBuilder->buildMenu($this->twig, 'main');

        $lastItem = DeepCollection::deepMake($menu)->last();

        $this->assertSame('The Bolt site', $lastItem->get('label'));
        $this->assertSame('Visit the excellent Bolt website!', $lastItem->get('title'));
        $this->assertSame('https://boltcms.io', $lastItem->get('link'));
        $this->assertSame('bolt-site', $lastItem->get('class'));
        $this->assertNull($lastItem->get('submenu'));
        $this->assertSame('https://boltcms.io', $lastItem->get('uri'));
        $this->assertNull($lastItem->get('foobar'));
    }

    public function testHasSubMenu(): void
    {
        $menu = $this->menuBuilder->buildMenu($this->twig, 'main');

        $submenu = $menu[1]['submenu'];

        $this->assertCount(4, $submenu);
    }
}

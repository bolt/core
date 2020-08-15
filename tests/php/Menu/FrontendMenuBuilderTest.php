<?php

declare(strict_types=1);

namespace Bolt\Tests\Menu;

use Bolt\Collection\DeepCollection;
use Bolt\Menu\FrontendMenuBuilder;
use Bolt\Tests\DbAwareTestCase;

class FrontendMenuBuilderTest extends DbAwareTestCase
{
    /** @var FrontendMenuBuilder */
    private $menuBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menuBuilder = self::$container->get(FrontendMenuBuilder::class);
    }

    public function testNonExistingMenu(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->menuBuilder->buildMenu('foo');
    }

    public function testDefaultMenuIsFirst(): void
    {
        $menu1 = $this->menuBuilder->buildMenu();
        $menu2 = $this->menuBuilder->buildMenu('main');

        $this->assertSame($menu1, $menu2);
    }

    public function testFirstItem(): void
    {
        $menu = $this->menuBuilder->buildMenu('main');

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
        $menu = $this->menuBuilder->buildMenu('main');

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
        $menu = $this->menuBuilder->buildMenu('main');

        $submenu = $menu[1]['submenu'];

        $this->assertCount(4, $submenu);
    }
}

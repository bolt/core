<?php

declare(strict_types=1);

namespace Bolt\Tests\Menu;

use Bolt\Collection\DeepCollection;
use Bolt\Menu\FrontendMenuBuilder;
use Bolt\Tests\DbAwareTestCase;

class FrontendMenuBuilderTest extends DbAwareTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var FrontendMenuBuilder menu */
        $this->menuBuilder = self::$container->get(\Bolt\Menu\FrontendMenuBuilder::class);
    }

    public function testNonExistingMenu(): void
    {
        $menu = $this->menuBuilder->getMenu('foo');

        $this->assertNull($menu);
    }

    public function testExistingMenu(): void
    {
        $menu = $this->menuBuilder->getMenu();

        $this->assertInstanceOf(DeepCollection::class, $menu);
    }

    public function testDefaultMenuIsFirst(): void
    {
        $menu1 = $this->menuBuilder->getMenu();
        $menu2 = $this->menuBuilder->getMenu('main');

        $this->assertSame($menu1, $menu2);
    }

    public function testFirstItem(): void
    {
        $menu = $this->menuBuilder->getMenu('main');

        $firstItem = $menu->first();

        $this->assertSame('Home', $firstItem->get('label'));
        $this->assertSame('This is the <b>first<b> menu item.', $firstItem->get('title'));
        $this->assertSame('homepage', $firstItem->get('link'));
        $this->assertSame('homepage', $firstItem->get('class'));
        $this->assertNull($firstItem->get('submenu'));
        $this->assertSame('/', $firstItem->get('uri'));
        $this->assertFalse($firstItem->get('current'));
        $this->assertNull($firstItem->get('foobar'));
    }

    public function testLastItem(): void
    {
        $menu = $this->menuBuilder->getMenu('main');

        $lastItem = $menu->last();

        $this->assertSame('The Bolt site', $lastItem->get('label'));
        $this->assertSame('Visit the excellent Bolt website!', $lastItem->get('title'));
        $this->assertSame('https://bolt.cm', $lastItem->get('link'));
        $this->assertSame('bolt-site', $lastItem->get('class'));
        $this->assertNull($lastItem->get('submenu'));
        $this->assertSame('https://bolt.cm', $lastItem->get('uri'));
        $this->assertFalse($lastItem->get('current'));
        $this->assertNull($lastItem->get('foobar'));
    }

    public function testHasSubMenu(): void
    {
        $menu = $this->menuBuilder->getMenu('main');

        $submenu = $menu->get('1')->get('submenu');

        $this->assertInstanceOf(DeepCollection::class, $submenu);
        $this->assertCount(4, $submenu);
    }
}

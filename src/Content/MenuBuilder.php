<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class MenuBuilder
{
    /** @var FactoryInterface */
    private $factory;

    /** @var Config */
    private $config;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var ContentRepository */
    private $content;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface      $factory
     * @param Config                $config
     * @param Stopwatch             $stopwatch
     * @param ContentRepository     $content
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(FactoryInterface $factory, Config $config, Stopwatch $stopwatch, ContentRepository $content, UrlGeneratorInterface $urlGenerator)
    {
        $this->config = $config;
        $this->factory = $factory;
        $this->stopwatch = $stopwatch;
        $this->content = $content;
        $this->urlGenerator = $urlGenerator;
    }

    public function createSidebarMenu()
    {
        $this->stopwatch->start('bolt.sidebar');

        $menu = $this->factory->createItem('root');

        $menu->addChild('Dashboard', ['uri' => 'homepage', 'extras' => [
            'name' => 'Dashboard',
            'icon_one' => 'fa-tachometer-alt',
            'link' => '/bolt/',
        ]]);

        $menu->addChild('Content', ['uri' => 'content', 'extras' => [
            'name' => 'Content',
            'type' => 'separator',
            'icon_one' => 'fa-file',
        ]]);

        $contenttypes = $this->config->get('contenttypes');

        foreach ($contenttypes as $contenttype) {
            $menu->addChild($contenttype['slug'], ['uri' => 'homepage', 'extras' => [
                'name' => $contenttype['name'],
                'singular_name' => $contenttype['singular_name'],
                'slug' => $contenttype['slug'],
                'singular_slug' => $contenttype['singular_slug'],
                'icon_one' => $contenttype['icon_one'],
                'icon_many' => $contenttype['icon_many'],
                'link' => $this->urlGenerator->generate('bolt_contentlisting', ['contenttype' => $contenttype['slug']]),
                'link_new' => $this->urlGenerator->generate('bolt_edit_record', ['id' => $contenttype['slug']]),
                'contenttype' => $contenttype['slug'],
                'singleton' => $contenttype['singleton'],
                'active' => $contenttype['slug'] === 'pages' ? true : false,
                'records' => $this->getLatestRecords($contenttype['slug']),
            ]]);
        }

        $menu->addChild('Settings', ['uri' => 'settings', 'extras' => [
            'name' => 'Settings',
            'type' => 'separator',
            'icon_one' => 'fa-wrench',
        ]]);

        $menu->addChild('Configuration', ['uri' => 'configuration', 'extras' => [
            'name' => 'Configuration',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/config',
        ]]);

        $menu->addChild('Content Files', ['uri' => 'content-files', 'extras' => [
            'name' => 'Content Files',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/files',
        ]]);

        $menu->addChild('Theme Files', ['uri' => 'theme-files', 'extras' => [
            'name' => 'Theme Files',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/themes',
        ]]);

        $menu->addChild('Users', ['uri' => 'users', 'extras' => [
            'name' => 'Users',
            'icon_one' => 'fa-users',
            'link' => '/bolt/users',
        ]]);

        $this->stopwatch->stop('bolt.sidebar');

        return $menu;
    }

    private function getLatestRecords($slug)
    {
        /** @var ContentType $ct */
        $contenttype = ContentTypeFactory::get($slug, $this->config->get('contenttypes'));

        /** @var Content $records */
        $records = $this->content->findAll(1, $contenttype);

        $result = [];

        /** @var Content $record */
        foreach ($records as $record) {
            $result[] = [
                'id' => $record->getId(),
                'title' => $record->magicTitle(),
                'link' => $record->magicLink(),
                'editlink' => $record->magicEditLink(),
            ];
        }

        return $result;
    }

    public function getMenu()
    {
        $menu = $this->createSidebarMenu()->getChildren();

        $menuData = [];

        foreach ($menu as $child) {
            $menuData[] = [
                'name' => $child->getExtra('name') ?: $child->getLabel(),
                'singular_name' => $child->getExtra('singular_name'),
                'slug' => $child->getExtra('slug'),
                'singular_slug' => $child->getExtra('singular_slug'),
                'icon_one' => $child->getExtra('icon_one'),
                'icon_many' => $child->getExtra('icon_many'),
                'link' => $child->getExtra('link'),
                'link_new' => $child->getExtra('link_new'),
                'contenttype' => $child->getExtra('contenttype'),
                'singleton' => $child->getExtra('singleton'),
                'type' => $child->getExtra('type'),
                'active' => $child->getExtra('active'),
                'records' => $child->getExtra('records'),
            ];
        }

        return $menuData;
    }
}

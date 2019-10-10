<?php

namespace spec\Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Menu\BackendMenuBuilder;
use Bolt\Repository\ContentRepository;
use Bolt\Twig\ContentExtension;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @mixin BackendMenuBuilder
 */
class BackendMenuBuilderSpec extends ObjectBehavior
{
    const TEST_TITLE = 'Test title';
    const TEST_SLUG = 'test-title';

    function let(
        FactoryInterface $menuFactory,
        Config $config,
        ContentRepository $contentRepository,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        ContentExtension $contentExtension
    ) {

        $this->beConstructedWith(
            $menuFactory,
            $config,
            $contentRepository,
            $urlGenerator,
            $translator,
            $contentExtension
        );
    }

    function it_builds_admin_menu(
        ContentExtension $contentExtension,
        Content $content,
        ContentRepository $contentRepository,
        Config $config,
        ContentType $contentType,
        FactoryInterface $menuFactory,
        ItemInterface $item,
        ItemInterface $subitem
    ) {
        $contentExtension->getTitle($content)
            ->shouldBeCalled()
            ->willReturn(self::TEST_TITLE);
        $contentExtension->getLink($content)
            ->shouldBeCalled()
            ->willReturn('/'.self::TEST_SLUG);
        $contentExtension->getEditLink($content)
            ->shouldBeCalled()
            ->willReturn('/bolt/edit-by-slug/'.self::TEST_SLUG);
        $contentRepository->findLatest($contentType, 1, BackendMenuBuilder::MAX_LATEST_RECORDS)
            ->shouldBeCalled();
//            ->willReturn(new Pagerfanta(new ArrayAdapter([])));

        $contentType->getSlug()->willReturn(self::TEST_SLUG);
        $contentType->offsetGet(Argument::type('string'))->shouldBeCalled();
        $config->get('contenttypes')->willReturn([$contentType]);

        $item->getChild(Argument::type('string'))->willReturn($subitem);
        $item->addChild(Argument::type('string'), Argument::type('array'))
            ->shouldBeCalled();
        $item->getChildren()->willReturn([$subitem]);

        $subitem->addChild(Argument::type('string'), Argument::type('array'))
            ->shouldBeCalled();
        $subitem->hasChildren()->shouldBeCalled()->willReturn(false);
        $subitem->getExtra(Argument::type('string'))->shouldBeCalled();
        $subitem->getLabel()->shouldBeCalled();
        $subitem->getUri()->shouldBeCalled();


        $menuFactory->createItem('root')->willReturn($item);

        $this->buildAdminMenu();
    }
}

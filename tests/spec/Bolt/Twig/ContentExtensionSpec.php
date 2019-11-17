<?php

declare(strict_types=1);

namespace spec\Bolt\Twig;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\Excerptable;
use Bolt\Entity\Field\ImageField;
use Bolt\Entity\Field\TextField;
use Bolt\Repository\ContentRepository;
use Bolt\Twig\ContentExtension;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @mixin ContentExtension
 */
class ContentExtensionSpec extends ObjectBehavior
{
    public const TEST_TITLE = 'test title';
    public const TEST_IMAGE = 'kitten.jpg';
    public const TEST_EXCERPT = 'test excerpt';
    public const TEST_LINK = 'test/link';
    public const TEST_FULL_LINK = 'http://localhost/test/link';
    public const TEST_SLUG = 'test-slug';
    public const TEST_CT_SLUG = 'ct-slug';
    public const TEST_ID = 42;

    public function let(UrlGeneratorInterface $urlGenerator, ContentRepository $contentRepository, CsrfTokenManagerInterface $csrfTokenManager): void
    {
        $this->beConstructedWith($urlGenerator, $contentRepository, $csrfTokenManager);
    }

    public function it_gets_title(Content $content, TextField $field, ContentType $definition): void
    {
        $definition->has('title_format')->shouldBeCalled()->willReturn(false);
        $content->getDefinition()->willReturn($definition);
        $field->__toString()->shouldBeCalled()->willReturn(self::TEST_TITLE);
        $content->hasField('title')->shouldBeCalled()->willReturn(true);
        $content->getField('title')->shouldBeCalled()->willReturn($field);

        $this->getTitle($content)->shouldBe(self::TEST_TITLE);
    }

    public function it_gets_title_from_other_title_field(Content $content, TextField $field, ContentType $definition): void
    {
        $definition->has('title_format')->shouldBeCalled()->willReturn(false);
        $content->getDefinition()->willReturn($definition);
        $field->__toString()->shouldBeCalled()->willReturn(self::TEST_TITLE);
        $content->hasField(Argument::type('string'))->willReturn(false);
        $content->hasField('titel')->shouldBeCalled()->willReturn(true);
        $content->getField('titel')->shouldBeCalled()->willReturn($field);

        $this->getTitle($content)->shouldBe(self::TEST_TITLE);
    }

    public function it_gets_title_without_title_field(Content $content, TextField $field, ContentType $definition): void
    {
        $definition->has('title_format')->shouldBeCalled()->willReturn(true);
        $definition->get('title_format')->shouldBeCalled()->willReturn(['other_text_field']);
        $content->getDefinition()->willReturn($definition);
        $field->__toString()->shouldBeCalled()->willReturn(self::TEST_TITLE);
        $content->hasFieldDefined('other_text_field')->shouldBeCalled()->willReturn(true);
        $content->hasField('other_text_field')->shouldBeCalled()->willReturn(true);
        $content->getField('other_text_field')->shouldBeCalled()->willReturn($field);

        $this->getTitle($content)->shouldBe(self::TEST_TITLE);
    }

    public function it_gets_image(Content $content, ImageField $field, Field $otherField): void
    {
        $content->getFields()->shouldBeCalled()->willReturn(new ArrayCollection([
            $otherField->getWrappedObject(),
            $field->getWrappedObject(),
        ]));

        $this->getImage($content)->shouldBe($field);
    }

    public function it_gets_image_path(Content $content, ImageField $field, Field $otherField): void
    {
        $field->getPath()->shouldBeCalled()->willReturn(self::TEST_IMAGE);
        $content->getFields()->shouldBeCalled()->willReturn(new ArrayCollection([
            $otherField->getWrappedObject(),
            $field->getWrappedObject(),
        ]));

        $this->getImage($content, true)->shouldBe(self::TEST_IMAGE);
    }

    public function it_gets_excerpt(Content $content, Excerptable $field, TextField $titleField, Field $otherField, ContentType $definition): void
    {
        $definition->has('title_format')->shouldBeCalled()->willReturn(false);
        $content->getDefinition()->willReturn($definition);
        $content->hasField('title')->shouldBeCalled()->willReturn(true);
        $content->getField('title')->shouldBeCalled()->willReturn($titleField);
        $titleField->getName()->willReturn('title');
        $titleField->__toString()->willReturn(self::TEST_TITLE);
        $field->__toString()->shouldBeCalled()->willReturn(self::TEST_EXCERPT);
        $field->getName()->willReturn('body');
        $otherField->__toString()->shouldNotBeCalled();
        $content->getFields()->shouldBeCalled()->willReturn(new ArrayCollection([
            $otherField->getWrappedObject(),
            $titleField->getWrappedObject(),
            $field->getWrappedObject(),
        ]));

        $this->getExcerpt($content)->shouldBe(self::TEST_TITLE . '. ' . self::TEST_EXCERPT);
    }

    public function it_gets_excerpt_without_excerptable_field(Content $content, Field $otherField, ContentType $definition): void
    {
        $definition->has('title_format')->shouldBeCalled()->willReturn(false);
        $content->getDefinition()->willReturn($definition);
        $content->hasField(Argument::type('string'))->shouldBeCalled()->willReturn(false);
        $content->getFields()->shouldBeCalled()->willReturn(new ArrayCollection([
            $otherField->getWrappedObject(),
        ]));

        $this->getExcerpt($content)->shouldBe('');
    }

    public function it_gets_link(Content $content, UrlGeneratorInterface $urlGenerator): void
    {
        $urlGenerator->generate(
            'record',
            [
                'slugOrId' => self::TEST_SLUG,
                'contentTypeSlug' => self::TEST_CT_SLUG,
            ],
            UrlGeneratorInterface::ABSOLUTE_PATH
        )->shouldBeCalled()->willReturn(self::TEST_LINK);
        $content->getId()->shouldBeCalled()->willReturn(self::TEST_ID);
        $content->getSlug()->shouldBeCalled()->willReturn(self::TEST_SLUG);
        $content->getContentTypeSingularSlug()->shouldBeCalled()->willReturn(self::TEST_CT_SLUG);

        $this->getLink($content)->shouldBe(self::TEST_LINK);
    }

    public function it_gets_absolute_link(Content $content, UrlGeneratorInterface $urlGenerator): void
    {
        $urlGenerator->generate(
            'record',
            [
                'slugOrId' => self::TEST_ID,
                'contentTypeSlug' => self::TEST_CT_SLUG,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        )->shouldBeCalled()->willReturn(self::TEST_FULL_LINK);
        $content->getId()->shouldBeCalled()->willReturn(self::TEST_ID);
        $content->getSlug()->shouldBeCalled()->willReturn(null);
        $content->getContentTypeSingularSlug()->shouldBeCalled()->willReturn(self::TEST_CT_SLUG);

        $this->getLink($content, true)->shouldBe(self::TEST_FULL_LINK);
    }

    public function it_doesnt_get_link_if_no_id(Content $content): void
    {
        $content->getId()->shouldBeCalled()->willReturn(null);
        $this->getLink($content)->shouldBe(null);
    }

    public function it_gets_edit_link(Content $content, UrlGeneratorInterface $urlGenerator): void
    {
        $urlGenerator->generate(
            'bolt_content_edit',
            ['id' => self::TEST_ID],
            UrlGeneratorInterface::ABSOLUTE_PATH
        )->shouldBeCalled()->willReturn(self::TEST_LINK);
        $content->getId()->shouldBeCalled()->willReturn(self::TEST_ID);

        $this->getEditLink($content)->shouldBe(self::TEST_LINK);
    }

    public function it_gets_absolute_edit_link(Content $content, UrlGeneratorInterface $urlGenerator): void
    {
        $urlGenerator->generate(
            'bolt_content_edit',
            ['id' => self::TEST_ID],
            UrlGeneratorInterface::ABSOLUTE_URL
        )->shouldBeCalled()->willReturn(self::TEST_FULL_LINK);
        $content->getId()->shouldBeCalled()->willReturn(self::TEST_ID);

        $this->getEditLink($content, true)->shouldBe(self::TEST_FULL_LINK);
    }

    public function it_doesnt_get_edit_link_if_no_id(Content $content): void
    {
        $content->getId()->shouldBeCalled()->willReturn(null);
        $this->getEditLink($content)->shouldBe(null);
    }

    public function it_gets_previous_content(Content $content, Content $previousContent, ContentRepository $contentRepository): void
    {
        $contentRepository->findAdjacentBy(
            'id',
            'previous',
            self::TEST_ID,
            self::TEST_CT_SLUG
        )->shouldBeCalled()->willReturn($previousContent);
        $content->getId()->shouldBeCalled()->willReturn(self::TEST_ID);
        $content->getContentType()->shouldBeCalled()->willReturn(self::TEST_CT_SLUG);

        $this->getPreviousContent($content)->shouldBe($previousContent);
    }

    public function it_gets_next_content(Content $content, Content $nextContent, ContentRepository $contentRepository): void
    {
        $contentRepository->findAdjacentBy(
            'id',
            'next',
            self::TEST_ID,
            null
        )->shouldBeCalled()->willReturn($nextContent);
        $content->getId()->shouldBeCalled()->willReturn(self::TEST_ID);

        $this->getNextContent($content, 'id', false)->shouldBe($nextContent);
    }
}

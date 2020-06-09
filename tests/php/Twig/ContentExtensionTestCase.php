<?php

declare(strict_types=1);

namespace Bolt\Tests\Twig;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Tests\DbAwareTestCase;
use Bolt\Twig\ContentExtension;
use Bolt\Twig\FieldExtension;
use PHPUnit\Framework\MockObject\MockObject;

class ContentExtensionTestCase extends DbAwareTestCase
{
    /** @var FieldExtension */
    private $extension;

    /** @var MockObject */
    private $content;

    /** @var MockObject */
    private $definition;

    /** @var MockObject */
    private $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = self::$container->get(ContentExtension::class);
        $this->content = $this->createMock(Content::class);
        $this->definition = $this->createMock(ContentType::class);
        $this->content->method('getDefinition')
            ->willReturn($this->definition);
        $this->field = $this->createMock(Field::class);
    }

    public function testTitle(): void
    {
        $this->definition->method('has')
            ->withConsecutive(['title_format'])
            ->willReturn(true);
        $this->definition->method('get')
            ->withConsecutive(['title_format'])
            ->willReturn('{number}: {title}');
        $this->content->method('getId')
            ->willReturn(1);
        $this->content->method('hasField')
            ->withConsecutive(['number'], ['title'])
            ->willReturnOnConsecutiveCalls(false, true);
        $this->content->method('getField')
            ->withConsecutive(['title'])
            ->willReturn($this->field);
        $this->field->method('isTranslatable')
            ->willReturn(false);
        $this->field->method('__toString')
            ->willReturn("Hey, this is a title");

        $this->assertSame('(unknown): Hey, this is a title', $this->extension->getTitle($this->content, 'en'));
    }

    public function testTitleFields(): void
    {
        $this->definition->method('has')
        ->withConsecutive(['title_format'])
        ->willReturn(true);
        $this->definition->method('get')
            ->withConsecutive(['title_format'])
            ->willReturn('{number}: {title}');
        $this->content->method('getId')
            ->willReturn(1);
        $this->content->method('hasField')
            ->withConsecutive(['number'], ['title'])
            ->willReturnOnConsecutiveCalls(false, true);

        $this->assertSame(['number', 'title'], $this->extension->getTitleFieldsNames($this->content));
    }
}

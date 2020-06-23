<?php

declare(strict_types=1);

namespace Bolt\Tests\Twig;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\ImageField;
use Bolt\Entity\Field\ImagelistField;
use Bolt\Entity\Field\TextField;
use Bolt\Tests\DbAwareTestCase;
use Bolt\Twig\ContentExtension;
use Bolt\Twig\FieldExtension;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Environment;

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
            ->willReturn('Hey, this is a title');

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

    public function testContentImage(): void
    {
        $field1 = $this->createMock(Field::class);
        $field2 = $this->createMock(Field::class);
        $imagefield = $this->createMock(ImageField::class);
        $field3 = $this->createMock(Field::class);

        $this->content->method('getFields')
            ->willReturn(new ArrayCollection([$field1, $field2, $imagefield, $field3]));

        $this->assertNull($this->extension->getImage($this->content));

        $imagefield->method('get')
            ->withConsecutive(['filename'])
            ->willReturn('example.jpg');
        $this->assertSame($imagefield, $this->extension->getImage($this->content));
    }

    public function testContentImageWithImagelist(): void
    {
        $field1 = $this->createMock(Field::class);
        $field2 = $this->createMock(Field::class);
        $image1 = $this->createMock(ImageField::class);
        $image1->method('get')
            ->withConsecutive(['filename'])
            ->willReturn('testimage.jpg');
        $image2 = $this->createMock(ImageField::class);
        $imagelist = $this->createMock(ImagelistField::class);
        $field3 = $this->createMock(Field::class);
        $imagelist->method('getValue')
            ->willReturn([$image1, $image2]);
        $this->content->method('getFields')
            ->willReturn(new ArrayCollection([$field1, $field2, $imagelist, $field3]));

        $this->assertSame($image1, $this->extension->getImage($this->content));
    }

    public function testExcerptOnString(): void
    {
        $this->assertSame('This is an exc…', $this->extension->getExcerpt('This is an excerpt as a string', 15));
    }

    public function testExceptFromFormatShort(): void
    {
        $this->definition->method('get')
            ->withConsecutive(['excerpt_format'])
            ->willReturn('{subheading}: {body}');

        $this->content->method('hasField')
            ->withConsecutive(['subheading'], ['body'])
            ->willReturnOnConsecutiveCalls(true, true);

        $field1 = $this->createMock(Field::class);
        $field2 = $this->createMock(Field::class);
        $field1->method('__toString')->willReturn("In this week's news");
        $field2->method('__toString')->willReturn('Bolt 4 is pretty awesome.');
        $this->content->method('getField')
            ->withConsecutive(['subheading'], ['body'])
            ->willReturnOnConsecutiveCalls($field1, $field2);
        $this->definition->method('has')
            ->withConsecutive(['excerpt_format'], ['subheading'], ['body'])
            ->willReturn(true);
        $this->content->method('getId')
            ->willReturn(1);

        $this->assertSame("In this week's ne…", $this->extension->getExcerpt($this->content, 18));
    }

    public function testExceptFromFormatFull(): void
    {
        $this->definition->method('get')
            ->withConsecutive(['excerpt_format'])
            ->willReturn('{subheading}: {body}');

        $this->content->method('hasField')
            ->withConsecutive(['subheading'], ['body'])
            ->willReturnOnConsecutiveCalls(true, true);

        $field1 = $this->createMock(Field::class);
        $field2 = $this->createMock(Field::class);
        $field1->method('__toString')->willReturn("In this week's news");
        $field2->method('__toString')->willReturn('Bolt 4 is pretty awesome.');
        $this->content->method('getField')
            ->withConsecutive(['subheading'], ['body'])
            ->willReturnOnConsecutiveCalls($field1, $field2);
        $this->definition->method('has')
            ->withConsecutive(['excerpt_format'], ['subheading'], ['body'])
            ->willReturn(true);
        $this->content->method('getId')
            ->willReturn(1);

        $this->assertSame("In this week's news: Bolt 4 is pretty awesome", $this->extension->getExcerpt($this->content));
    }

    public function testExcerptNoFormat(): void
    {
        $title = $this->createConfiguredMock(TextField::class, [
            'getName' => 'title',
            '__toString' => 'This field should not be used',
        ]);

        $subheading = $this->createConfiguredMock(TextField::class, [
            'getName' => 'subheading',
            '__toString' => 'This subheading is OK.',
        ]);

        $body = $this->createConfiguredMock(TextField::class, [
            'getName' => 'body',
            '__toString' => 'Here is the long body. It is OK too.',
        ]);

        $this->content->method('getFields')
            ->willReturn(new ArrayCollection([$title, $subheading, $body]));

        $this->assertSame('This subheading is OK. Here is the long body. It is OK too', $this->extension->getExcerpt($this->content));
        $this->assertSame('This subheading is OK. Here…', $this->extension->getExcerpt($this->content, 28));
    }

    public function testIsCurrentGlobalTwig(): void
    {
        $envCurrent = $this->createConfiguredMock(Environment::class, [
            'getGlobals' => [
                'record' => $this->content,
            ],
        ]);

        $notCurrentContent = $this->createMock(Content::class);

        $envNotCurrent = $this->createConfiguredMock(Environment::class, [
            'getGlobals' => [
                'record' => $notCurrentContent,
            ],
        ]);

        $this->assertTrue($this->extension->isCurrent($envCurrent, $this->content));
        $this->assertFalse($this->extension->isCurrent($envNotCurrent, $this->content));
    }
}

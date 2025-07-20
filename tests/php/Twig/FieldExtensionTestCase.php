<?php

declare(strict_types=1);

namespace Bolt\Tests\Twig;

use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Field;
use Bolt\Entity\Field\HtmlField;
use Bolt\Tests\DbAwareTestCase;
use Bolt\Twig\FieldExtension;
use PHPUnit\Framework\MockObject\MockObject;

class FieldExtensionTestCase extends DbAwareTestCase
{
    /** @var FieldExtension */
    private $extension;
    /** @var MockObject */
    private $field;
    /** @var MockObject */
    private $fieldType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = self::$container->get(FieldExtension::class);

        $this->field = $this->createMock(Field::class);
        $this->fieldType = $this->createMock(FieldType::class);
        $this->field->method('getDefinition')
            ->willReturn($this->fieldType);
    }

    public function testFieldLabel(): void
    {
        $this->fieldType->method('get')
            ->withConsecutive(['label'])
            ->wilLReturn('Test field');

        $this->assertSame('Test field', $this->extension->getLabel($this->field));
    }

    public function testFieldType(): void
    {
        $this->fieldType->method('get')
            ->withConsecutive(['type'])
            ->willReturn('embed');

        $this->assertSame('embed', $this->extension->getType($this->field));
    }

    public function testFactory(): void
    {
        $definition = collect([
            'type' => 'html',
            'something' => 'else',
        ]);

        $actual = $this->extension->fieldFactory('testfield', $definition);

        $this->assertTrue($actual instanceof HtmlField);
        $this->assertTrue($actual->getDefinition() instanceof FieldType);
        $this->assertSame('else', $actual->getDefinition()->get('something'));
    }

    public function testsFiltersExist(): void
    {
        $actual = collect($this->extension->getFilters())->transform(function ($filter) {
            return $filter->getName();
        })->toArray();

        $expected = ['label', 'type', 'selected'];

        $this->assertSame($expected, $actual);
    }

    public function testFunctionsExist(): void
    {
        $actual = collect($this->extension->getFunctions())->transform(function ($function) {
            return $function->getName();
        })->toArray();

        $expected = ['field_factory'];

        $this->assertSame($expected, $actual);
    }
}

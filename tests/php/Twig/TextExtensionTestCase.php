<?php

declare(strict_types=1);

namespace Bolt\Tests\Twig;

use Bolt\Tests\DbAwareTestCase;
use Bolt\Twig\TextExtension;

class TextExtensionTestCase extends DbAwareTestCase
{
    /** @var TextExtension */
    private $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = self::getContainer()->get(TextExtension::class);
    }

    public function testPlainText(): void
    {
        $this->assertSame('escaped text', $this->extension->plainText('escaped text'));
        $this->assertSame('escaped text', $this->extension->plainText('<span class="some-class">escaped text</span>'));
    }

    public function testSafeString(): void
    {
        $this->assertSame('sabeg', $this->extension->safeString('saбeг'));
        $this->assertSame('saefe', $this->extension->safeString('säfe'));
        $this->assertSame('safe', $this->extension->safeString('sa|||f|*e'));
    }

    public function testSlug(): void
    {
        $this->assertSame('john-doe', $this->extension->slug('John Doe'));
        $this->assertSame('b-o-e', $this->extension->slug('б o ê'));
    }

    public function testUcwords(): void
    {
        $this->assertSame('LowErCase', $this->extension->ucwords('lowErCase'));
        $this->assertSame('Lowercase more words', $this->extension->ucwords('lowercase more words'));
    }
}

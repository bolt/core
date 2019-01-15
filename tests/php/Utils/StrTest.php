<?php

declare(strict_types=1);

namespace Bolt\Tests\Utils;

use Bolt\Utils\Str;

class StrTest extends \PHPUnit\Framework\TestCase
{
    public function testSlug(): void
    {
        $slug = Str::slug('test 1');
        $this->assertSame('test-1', $slug);

        $slug = Str::slug('test  2');
        $this->assertSame('test-2', $slug);

        $slug = Str::slug('test -  - __ ---  3');
        $this->assertSame('test-3', $slug);

        $slug = Str::slug('This is a title');
        $this->assertSame('this-is-a-title', $slug);

        $slug = Str::slug('Hēävy METÄL ümlåü†!!! 🤘');
        $this->assertSame('heaevy-metael-uemlaaue', $slug);

        $slug = Str::slug('Крещение Господне: истинная вера и традиции');
        $this->assertSame('kreshchenie-gospodne-istinnaya-vera-i-tradicii', $slug);
    }
}

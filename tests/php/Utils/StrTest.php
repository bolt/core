<?php

declare(strict_types=1);

namespace Bolt\Tests\Utils;

class StrTest extends \PHPUnit\Framework\TestCase
{
    public function testSlug()
    {
        $slug = \Bolt\Utils\Str::slug('test 1');
        $this->assertEquals('test-1', $slug);
    }
}
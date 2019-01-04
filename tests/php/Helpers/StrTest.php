<?php

declare(strict_types=1);

namespace Bolt\Tests\Helpers;

use Bolt\Helpers\Str;

class StrTest extends \PHPUnit\Framework\TestCase
{
    public function testSlug()
    {
        $slug = Str::slug('test 1');
        $this->assertEquals('test-1', $slug);
    }
}
<?php

namespace Bolt\Tests\Utils;

use Bolt\Utils\Slugger;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the application utils.
 *
 * See https://symfony.com/doc/current/book/testing.html#unit-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ ./vendor/bin/phpunit
 */
class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $string, string $slug)
    {
        $this->assertSame($slug, Slugger::slugify($string));
    }

    public function getSlugs()
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield ['  Lorem Ipsum  ', 'lorem-ipsum'];
        yield [' lOrEm  iPsUm  ', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', '!lorem-ipsum!'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield ['lorem 日本語 ipsum', 'lorem-日本語-ipsum'];
        yield ['lorem русский язык ipsum', 'lorem-русский-язык-ipsum'];
        yield ['lorem العَرَبِيَّة‎‎ ipsum', 'lorem-العَرَبِيَّة‎‎-ipsum'];
    }
}

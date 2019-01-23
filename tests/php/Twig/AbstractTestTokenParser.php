<?php

declare(strict_types=1);

namespace Bolt\Tests\Twig;

use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Node\Node;
use Twig\Parser;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenStream;

/**
 * Abstract TokenParser test base.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 * @author Bob den Otter <bobdenotter@gmail.com>
 */
abstract class AbstractTestTokenParser extends \PHPUnit\Framework\TestCase
{
    protected function getParser(TokenStream $tokenStream, AbstractTokenParser $testParser): Parser
    {
        $env = new Environment($this->getMockBuilder(LoaderInterface::class)->getMock());
        $parser = new Parser($env);
        $parser->setParent(new Node());
        $env->addTokenParser($testParser);

        $p = new \ReflectionProperty($parser, 'stream');
        $p->setAccessible(true);
        $p->setValue($parser, $tokenStream);

        return $parser;
    }
}

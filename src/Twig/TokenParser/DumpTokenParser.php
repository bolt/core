<?php

declare(strict_types=1);

namespace Bolt\Twig\TokenParser;

use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Twig {% dump %} token parser.
 */
class DumpTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();

        if (! $this->parser->getStream()->test(Token::BLOCK_END_TYPE)) {
            $this->parser->getExpressionParser()->parseMultitargetExpression();
        }
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new TextNode('', $lineno);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'dump';
    }
}

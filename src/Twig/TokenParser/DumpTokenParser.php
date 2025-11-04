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
            while (true) {
                if (! $this->parser->getStream()->nextIf(Token::PUNCTUATION_TYPE, ',')) {
                    break;
                }
            }
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

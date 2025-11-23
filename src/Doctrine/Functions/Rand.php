<?php

declare(strict_types=1);

namespace Bolt\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class Rand extends FunctionNode
{
    private Node|string|null $expression = null;

    public function getSql(SqlWalker $sqlWalker): string
    {
        if ($this->expression instanceof Node) {
            // value is one if SQLite. See Bolt\Storage\Directive\RandomDirectiveHandler
            if (property_exists($this->expression, 'value') && $this->expression->value === '1') {
                return 'random()';
            }
            // value is two if PostgreSQL. See Bolt\Storage\Directive\RandomDirectiveHandler
            if (property_exists($this->expression, 'value') && $this->expression->value === '2') {
                return 'RANDOM()';
            }
        }

        return 'RAND()';
    }

    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        if ($lexer->lookahead?->type !== TokenType::T_CLOSE_PARENTHESIS) {
            $this->expression = $parser->SimpleArithmeticExpression();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

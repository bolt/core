<?php

declare(strict_types=1);

namespace Bolt\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\SimpleArithmeticExpression;
use Doctrine\ORM\Query\Lexer;

class Rand extends FunctionNode
{
    /** @var SimpleArithmeticExpression */
    private $expression = null;

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        // value is one if SQLite. See Bolt\Storage\Directive\RandomDirectiveHandler
        if (property_exists($this->expression, 'value') && $this->expression->value === '1') {
            return 'random()';
        }
        // value is two if PostgreSQL. See Bolt\Storage\Directive\RandomDirectiveHandler
        if (property_exists($this->expression, 'value') && $this->expression->value === '2') {
            return 'RANDOM()';
        }

        return 'RAND()';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        if ($lexer->lookahead['type'] !== Lexer::T_CLOSE_PARENTHESIS) {
            $this->expression = $parser->SimpleArithmeticExpression();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

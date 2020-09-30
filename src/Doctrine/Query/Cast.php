<?php

// some hints from
// https://stackoverflow.com/questions/7405342/casting-attributes-for-ordering-on-a-doctrine2-dql-query
// dig into https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/cookbook/advanced-field-value-conversion-using-custom-mapping-types.html
declare(strict_types=1);

namespace Bolt\Doctrine\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Cast extends FunctionNode
{
    /** @var \Doctrine\ORM\Query\AST\PathExpression */
    protected $first;
    /** @var string */
    protected $second;
    /** @var string */
    protected $backend_driver;

    public function getSql(SqlWalker $sqlWalker): string
    {
        $backend_driver = $sqlWalker->getConnection()->getDriver()->getName();
        // test if we are using MySQL
        if (mb_strpos($backend_driver, 'mysql') !== false) {
            // how do we know what type $this->first is? For now hardcoding
            // type(t.value) = JSON for MySQL. JSONB for others.
            // alternatively, test if true: $this->first->dispatch($sqlWalker)==='b2_.value'
            if ($this->first->identificationVariable === 't' && $this->first->field === 'value' && $this->second === 'TEXT') {
                return $this->first->dispatch($sqlWalker);
            }
        }

        return sprintf('CAST(%s AS %s)',
            $this->first->dispatch($sqlWalker),
            $this->second
            );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->first = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_AS);
        $parser->match(Lexer::T_IDENTIFIER);
        $this->second = $parser->getLexer()->token['value'];
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

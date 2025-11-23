<?php

declare(strict_types=1);

namespace Bolt\Doctrine\Query;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use RuntimeException;

class Cast extends FunctionNode
{
    protected Node|string $first;
    protected string $second;

    public function getSql(SqlWalker $sqlWalker): string
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        // test if we are using MySQL
        if ($platform instanceof MySQLPlatform) {
            // YES we are using MySQL
            // how do we know what type $this->first is? For now hardcoding
            // type(t.value) = JSON for MySQL. JSONB for others.
            // alternatively, test if true: $this->first->dispatch($sqlWalker)==='b2_.value',
            // b4_.value for /bolt/new/showcases
            if ($this->first->dispatch($sqlWalker) === 'b2_.value' ||
                $this->first->dispatch($sqlWalker) === 'b4_.value') {
                return $this->first->dispatch($sqlWalker);
            }
        }

        if ($platform instanceof SqlitePlatform && $this->second === 'TEXT') {
            $this->second = 'CHAR';
        }

        return sprintf(
            'CAST(%s AS %s)',
            $this->first->dispatch($sqlWalker),
            $this->second
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->first = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_AS);
        $parser->match(TokenType::T_IDENTIFIER);
        $this->second = $parser->getLexer()->token->value ?? throw new RuntimeException('Missing second CAST token');
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

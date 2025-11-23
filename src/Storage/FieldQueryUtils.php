<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Doctrine\Version;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;

class FieldQueryUtils
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function isFieldType(QueryInterface $query, string $fieldname, string $type): bool
    {
        if (in_array($fieldname, ['anyField', 'anything'], true)) {
            return false;
        }

        $contentType = $query->getConfig()->get('contenttypes/' . $query->getContentType());
        $definitionType = $contentType->get('fields')->get($fieldname)->get('type', false);

        return $definitionType === $type;
    }

    public function hasCast(): bool
    {
        $doctrineVersion = new Version($this->em->getConnection());

        return $doctrineVersion->hasCast();
    }

    public function hasJsonSearch(): bool
    {
        $doctrineVersion = new Version($this->em->getConnection());

        return $doctrineVersion->hasJsonSearch();
    }

    public function isLocalizedField(QueryInterface $query, $fieldname): bool
    {
        $contentType = $query->getConfig()->get('contenttypes/' . $query->getContentType());

        return $contentType->get('fields')->get($fieldname)->get('localize', false);
    }

    public function getNumericCastExpression(string $left): string
    {
        $expression = new Func('SUBSTRING', [$left, 3, (new Expr())->length($left)->__toString() . ' - 4']);

        return 'CAST(' . $expression->__toString() . ' as decimal)';
    }

    public function isSqlite(): bool
    {
        return $this->em->getConnection()->getDatabasePlatform() instanceof SQLitePlatform;
    }
}

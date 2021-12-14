<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Doctrine\Version;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;

class FieldQueryUtils
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        $expression = (new Expr())->substring($left, 3, (new Expr())->length($left)->__toString() . ' - 4')->__toString();

        return 'CAST(' . $expression . ' as decimal)';
    }
}

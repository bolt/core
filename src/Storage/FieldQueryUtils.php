<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Doctrine\Version;
use Bolt\Entity\Field\NumberField;
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

    public function isNumericField(QueryInterface $query, $fieldname): bool
    {
        $contentType = $query->getConfig()->get('contenttypes/' . $query->getContentType());
        $type = $contentType->get('fields')->get($fieldname)->get('type', false);

        return $type === NumberField::TYPE;
    }

    public function hasCast(): bool
    {
        $doctrineVersion = new Version($this->em->getConnection());

        return $doctrineVersion->hasCast();
    }

    public function isLocalizedField(QueryInterface $query, $fieldname): bool
    {
        $contentType = $query->getConfig()->get('contenttypes/' . $query->getContentType());

        return $contentType->get('fields')->get($fieldname)->get('localize', false);
    }

    public function getNumericCastExpression(string $left): string
    {
        $expression = (new Expr())->substring($left, 3, (new Expr())->length($left))->__toString();

        return 'CAST(' . $expression . ' as decimal)';
    }
}

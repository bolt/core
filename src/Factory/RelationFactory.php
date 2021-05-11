<?php

declare(strict_types=1);

namespace Bolt\Factory;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\RelationRepository;
use Tightenco\Collect\Support\Collection;

class RelationFactory
{
    /** @var RelationRepository */
    private $repository;

    /** @var Collection */
    private $relations;

    public function __construct(RelationRepository $repository)
    {
        $this->repository = $repository;
        $this->relations = collect([]);
    }

    public function create(Content $from, Content $to): Relation
    {
        $existing = $this->getExisting($from, $to);
        if ($existing instanceof Relation) {
            return $existing;
        }

        $new = new Relation($from, $to);
        $this->relations->add($new);

        return $new;
    }

    private function getExisting(Content $from, Content $to): ?Relation
    {
        $fromDb = $this->repository->findRelation($from, $to);
        $fromMemory = $this->getFromMemory($from, $to);

        return $fromDb ?? $fromMemory;
    }

    private function getFromMemory(Content $from, Content $to): ?Relation
    {
        return $this->relations->filter(function (Relation $relation) use ($from, $to) {
            return ($relation->getFromContent() === $from && $relation->getToContent() === $to)
                || ($relation->getToContent() === $to && $relation->getToContent() === $from);
        })->last(null, null);
    }
}

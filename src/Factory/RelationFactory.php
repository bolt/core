<?php

declare(strict_types=1);

namespace Bolt\Factory;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\RelationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Tightenco\Collect\Support\Collection;

class RelationFactory
{
    /** @var EntityManagerInterface */
    private $em; 

    /** @var RelationRepository */
    private $repository;

    /** @var Collection */
    private $relations;

    public function __construct(RelationRepository $repository, EntityManagerInterface $em)
    {
        $this->em = $em;
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

    /**
     * @param Relation|Relation[] $relation
     */
    public function save($relation): void
    {
        if ($relation instanceof Relation) {
            $this->em->persist($relation);

        } elseif (is_iterable($relation)) {
            foreach ($relation as $r) {
                $this->em->persist($r);
            }
        }

        $this->em->flush();
    }
}

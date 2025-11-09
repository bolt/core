<?php

declare(strict_types=1);

namespace Bolt\Factory;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\RelationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;

readonly class RelationFactory
{
    private Collection $relations;

    public function __construct(
        private RelationRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->relations = collect();
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
        return $this->relations->filter(
            fn (Relation $relation): bool => ($relation->getFromContent() === $from && $relation->getToContent() === $to)
                || ($relation->getToContent() === $to && $relation->getToContent() === $from)
        )->last();
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

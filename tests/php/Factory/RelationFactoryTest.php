<?php

declare(strict_types=1);

namespace Bolt\Tests\Factory;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Factory\RelationFactory;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\RelationRepository;
use Bolt\Tests\DbAwareTestCase;
use Doctrine\Common\Collections\Collection;

final class RelationFactoryTest extends DbAwareTestCase
{
    public function testSavePersistsTheRelation(): array
    {
        /** @var RelationRepository */
        $relationRepository = $this->getEm()->getRepository(Relation::class);

        /** @var RelationFactory $relationFactory */
        $relationFactory = new RelationFactory($relationRepository, $this->getEm());

        /** @var Content $page */
        $page = $this->getEm()->getRepository(Content::class)->findOneBy(['contentType' => 'pages']);

        /** @var Content|null $nonRelatedEntry */
        $nonRelatedEntry = $this->getNonRelatedEntryForPage($page);

        /** @var Relation $newRelation */
        $newRelation = $relationFactory->create($nonRelatedEntry, $page);

        $relationFactory->save($newRelation);

        $this->assertNotNull($newRelation->getId(), 'If id is null, the relation has not been persisted.');

        return [
            'page' => $page,
            'entry' => $nonRelatedEntry,
            'relation' => $newRelation,
        ];
    }

    /**
     * @depends testSavePersistsTheRelation
     */
    public function testPersistedRelationCascadesToContent(array $entities): void
    {
        /** @var Content $page */
        $page = $entities['page'];

        /** @var Content $entry */
        $entry = $entities['entry'];

        /** @var Relation $persistedRelation */
        $persistedRelation = $entities['relation'];

        /** @var array $entryRelationIds */
        $entryRelationIds = $this->getContentRelatedIds($entry->getRelationsFromThisContent());

        /** @var array $pageRelationIds */
        $pageRelationIds = $this->getContentRelatedIds($page->getRelationsToThisContent());

        $this->assertTrue(in_array($persistedRelation->getId(), $pageRelationIds, true), 'It seems like relation has not persisted for contentType pages');
        $this->assertTrue(in_array($persistedRelation->getId(), $entryRelationIds, true), 'It seems like relation has not persisted for contentType entries');
    }

    public function testSaveMultipleRelations(): void
    {
        /** @var RelationRepository */
        $relationRepository = $this->getEm()->getRepository(Relation::class);

        /** @var RelationFactory $relationFactory */
        $relationFactory = new RelationFactory($relationRepository, $this->getEm());

        /** @var ContentRepository $contentRepository */
        $contentRepository = $this->getEm()->getRepository(Content::class);

        /** @var Content $page */
        $page = $this->getEm()->getRepository(Content::class)->findOneBy(['contentType' => 'pages']);

        /** @var array $nonRelatedEntryIds */
        $nonRelatedEntryIds = $this->getNonRelatedEntryIds($page);

        $entries = [];
        $relations = [];

        $limit = count($nonRelatedEntryIds) > 5 ? 5 : count($nonRelatedEntryIds);
        for ($i = 0; $i < $limit; $i++) {
            /** @var Content|null $entry */
            $entry = $contentRepository->findOneById($nonRelatedEntryIds[$i]);
            $entries[] = $entry;
            $relations[] = $relationFactory->create($entry, $page);
        }

        $relationFactory->save($relations);
        $relationIds = array_map(function ($relation) {
            return $relation->getId();
        }, $relations);

        $this->assertFalse(in_array(null, $relationIds, true), 'Some of the relations has not been persisted.');
    }

    /**
     * Returns a Content entity with contentType 'Entry' that does not have a
     * relation with the argument.
     */
    private function getNonRelatedEntryForPage(Content $page): ?Content
    {
        $nonRelatedEntries = $this->getNonRelatedEntryIds($page);

        $randomNonRelatedEntryIndex = random_int(0, count($nonRelatedEntries) - 1);
        $randomNonRelatedEntryId = $nonRelatedEntries[$randomNonRelatedEntryIndex];

        return $this->getEm()->getRepository(Content::class)->findOneBy(['id' => $randomNonRelatedEntryId]);
    }

    private function getNonRelatedEntryIds(Content $page): array
    {
        $relations = $page->getRelationsToThisContent();
        $relatedIds = [];
        foreach ($relations as $relative) {
            $relatedIds[] = $relative->getId();
        }

        $entries = $this->getEm()->getRepository(Content::class)->findBy(['contentType' => 'entries']);
        $entryIds = array_map(function ($entry) {
            return $entry->getId();
        }, $entries);
        $nonRelatedEntries = array_filter($entryIds, function ($id) use ($relatedIds) {
            return ! in_array($id, $relatedIds, true);
        });

        reset($nonRelatedEntries);

        return $nonRelatedEntries;
    }

    private function getContentRelatedIds(Collection $contentRelations): array
    {
        $relatedIds = [];
        foreach ($contentRelations as $relation) {
            $relatedIds[] = $relation->getId();
        }

        return $relatedIds;
    }
}

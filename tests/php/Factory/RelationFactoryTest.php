<?php declare(strict_types=1);

namespace Bolt\Tests\Factory;


use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Factory\ContentFactory;
use Bolt\Factory\RelationFactory;
use Bolt\Tests\DbAwareTestCase;
use Doctrine\Common\Collections\Collection;

final class RelationFactoryTest extends DbAwareTestCase
{
    
    public function testSavePersistsTheRelation(): array
    {
        /** @var RelationFactory $relationFactory */
        $relationFactory = new RelationFactory($this->getEm()->getRepository(Relation::class), $this->getEm());

        /**
         * @var Content $page
         */
        $page = $this->getEm()->getRepository(Content::class)->findOneBy(['contentType' => 'pages']);
        $nonRelatedEntry = $this->getNonRelatedEntryForPage($page);

        

        /**
         * @var Relation $newRelation
         */
        $newRelation = $relationFactory->create($nonRelatedEntry, $page);

        $relationFactory->save($newRelation);

        $this->assertNotNull($newRelation->getId(), 'If id is null, the relation has not been persisted.');

        return [
            "page" => $page,
            "entry" => $nonRelatedEntry,
            "relation" => $newRelation
        ];
    }



    /**
     * @depends testSavePersistsTheRelation
     */
    public function testPersistedRelationCascadesToContent(array $entities): void
    {        
        /** @var Content $page */
        $page = $entities["page"];

        /** @var Content $entry */
        $entry = $entities["entry"];

        /** @var Relation $persistedRelation */
        $persistedRelation = $entities["relation"];

        /** @var array $entryRelationIds  */
        $entryRelationIds = $this->getContentRelatedIds($entry->getRelationsFromThisContent());
        
        /** @var array $pageRelationIds  */
        $pageRelationIds = $this->getContentRelatedIds($page->getRelationsToThisContent());
        
        $this->assertTrue(in_array($persistedRelation->getId(), $pageRelationIds), "It seems like relation has not persisted for contentType pages");
        $this->assertTrue(in_array($persistedRelation->getId(), $entryRelationIds), "It seems like relation has not persisted for contentType entries");

    }

    /**
     * Returns a Content entity with contentType 'Entry' that does not have a 
     * relation with the argument.
     * 
     * @return Content $nonRelatedEntry
     */
    private function getNonRelatedEntryForPage(Content $page): ?Content
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
        $nonRelatedEntries = array_filter($entryIds, fn ($id) => !in_array($id, $relatedIds));

        reset($nonRelatedEntries);

        $randomNonRelatedEntryIndex = random_int(0, count($nonRelatedEntries) - 1);
        $randomNonRelatedEntryId = $nonRelatedEntries[$randomNonRelatedEntryIndex];

        /** 
         * @var Content $nonRelatedEntry
         */
        $nonRelatedEntry = $this->getEm()->getRepository(Content::class)->findOneBy(['id' => $randomNonRelatedEntryId]);
        
        return $nonRelatedEntry;
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

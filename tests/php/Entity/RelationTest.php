<?php

declare(strict_types=1);

namespace Bolt\Tests\Entity;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Tests\DbAwareTestCase;

class RelationTest extends DbAwareTestCase
{
    public function testNullOnGetId(): void
    {
        /** @var Content $page */
        $page = $this->getEm()->getRepository(Content::class)->findOneBy(['contentType' => 'pages']);

        /** @var Content $entry */
        $entry = $this->getEm()->getRepository(Content::class)->findOneBy(['contentType' => 'entries']);

        $relation = new Relation($entry, $page);

        // A new entity will have null before being persisted.
        $this->assertNull($relation->getId());
    }
}

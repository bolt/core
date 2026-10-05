<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Frontend;

use Bolt\Entity\Content;
use Bolt\Tests\DbAwareTestCase;

/**
 * End-to-end: a singleton is reachable both at its slugless URL (`/about`) and at
 * the record-detail route (`/about/{slug}`). Both must declare the slugless URL
 * as their canonical, so the two URLs consolidate to one.
 */
class SingletonCanonicalTest extends DbAwareTestCase
{
    public function testSingletonRecordUrlCanonicalizesToSluglessUrl(): void
    {
        /** @var Content|null $record */
        $record = $this->getEm()->getRepository(Content::class)
            ->findOneBy(['contentType' => 'about']);

        $this->assertNotNull($record, 'Expected the "about" singleton fixture to be seeded.');

        $singularSlug = $record->getContentTypeSingularSlug();
        $slug = $record->getSlug();
        $expectedCanonical = 'http://localhost/' . $singularSlug;

        // The slugged record URL must canonicalize to the slugless singleton URL.
        $crawler = $this->client->request('GET', sprintf('/%s/%s', $singularSlug, $slug));
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            $expectedCanonical,
            $crawler->filter('link[rel="canonical"]')->attr('href')
        );

        // The slugless URL itself is self-referential (same canonical).
        $crawler = $this->client->request('GET', '/' . $singularSlug);
        $this->assertResponseIsSuccessful();
        $this->assertSame(
            $expectedCanonical,
            $crawler->filter('link[rel="canonical"]')->attr('href')
        );
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Frontend;

use Bolt\Tests\DbAwareTestCase;

/**
 * The listing canonical must be the clean listing URL. Query params parsed for
 * the listing (order/status/filters) are volatile and must not leak into the
 * <link rel="canonical">.
 */
class ListingCanonicalTest extends DbAwareTestCase
{
    public function testListingCanonicalOmitsQueryParams(): void
    {
        $crawler = $this->client->request('GET', '/showcases?order=title');

        $this->assertResponseIsSuccessful();

        $canonical = $crawler->filter('link[rel="canonical"]')->attr('href');

        $this->assertStringEndsWith('/showcases', $canonical);
        $this->assertStringNotContainsString('?', $canonical);
        $this->assertStringNotContainsString('order', $canonical);
    }
}

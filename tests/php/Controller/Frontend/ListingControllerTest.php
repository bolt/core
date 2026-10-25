<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Tests\DbAwareTestCase;

class ListingControllerTest extends DbAwareTestCase
{
    protected function setUp(): void
    {
        // Without a `canonical`, the CanonicalSubscriber chokes on frontend requests.
        putenv('BOLT_CANONICAL=http://localhost');
        $_SERVER['BOLT_CANONICAL'] = 'http://localhost';
        $_ENV['BOLT_CANONICAL'] = 'http://localhost';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        putenv('BOLT_CANONICAL');
        unset($_SERVER['BOLT_CANONICAL'], $_ENV['BOLT_CANONICAL']);

        parent::tearDown();
    }

    public function testListingRedirectsToDefaultLocaleWhenLocaleNotSupported(): void
    {
        // `pages` is localized to en/nl/ja/nb, so `fr` is a valid app locale but
        // not a valid one for this ContentType.
        $this->client->request('GET', '/fr/pages');
        $response = $this->client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertStringContainsString('/en/pages', (string) $response->headers->get('Location'));
    }

    public function testListingFallsBackToDefaultLocaleWhenForwardedWithoutRoute(): void
    {
        // A homepage listing is forwarded to ListingController without a `_route`,
        // so there's nothing to redirect to: it must render in the default locale.
        $this->setHomepage('pages');

        $this->client->request('GET', '/fr/');
        $response = $this->client->getResponse();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('<html lang="en"', (string) $response->getContent());
    }

    private function setHomepage(string $homepage): void
    {
        $config = self::getContainer()->get(Config::class);

        // Mutate only the `general` collection in place; rebuilding the whole data
        // tree would turn `contenttypes` into plain collections and break typing.
        $property = new \ReflectionProperty(Config::class, 'data');
        $property->getValue($config)->get('general')->put('homepage', $homepage);
    }
}

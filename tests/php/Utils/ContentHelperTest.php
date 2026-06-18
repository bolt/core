<?php

declare(strict_types=1);

namespace Bolt\Tests\Utils;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Twig\LocaleExtension;
use Bolt\Utils\ContentHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Canonical route/params resolution, with focus on the singleton handling:
 * a singleton is served at its slugless listing URL (ListingController forwards
 * a singleton listing to the record), so its canonical must point there
 * (`/{singularSlug}`) and not at the record-detail route (`/{singularSlug}/{slug}`).
 */
class ContentHelperTest extends TestCase
{
    public function testSingletonCanonicalUsesSluglessListingRoute(): void
    {
        $content = $this->createContent(
            singleton: true,
            singularSlug: 'contact',
            slug: 'contact-info',
            recordSlug: 'contact-2'
        );

        $canonical = $this->createMock(Canonical::class);
        $canonical->expects($this->once())
            ->method('setPath')
            ->with('listing_locale', [
                'contentTypeSlug' => 'contact',
                '_locale' => 'en',
            ]);

        $this->createContentHelper($canonical)->setCanonicalPath($content, 'en');
    }

    public function testNonSingletonCanonicalUsesRecordRouteWithSlug(): void
    {
        $content = $this->createContent(
            singleton: false,
            singularSlug: 'entry',
            slug: 'entries',
            recordSlug: 'my-post',
            recordRoute: 'record'
        );

        $canonical = $this->createMock(Canonical::class);
        $canonical->expects($this->once())
            ->method('setPath')
            ->with('record', [
                'contentTypeSlug' => 'entry',
                'slugOrId' => 'my-post',
                '_locale' => 'en',
            ]);

        $this->createContentHelper($canonical)->setCanonicalPath($content, 'en');
    }

    public function testHomepageCanonicalIsNotAffectedBySingletonHandling(): void
    {
        // The homepage is itself a singleton, but must keep resolving to the
        // homepage route, not the listing route.
        $content = $this->createContent(
            singleton: true,
            singularSlug: 'homepage',
            slug: 'homepage',
            recordSlug: 'homepage'
        );

        $canonical = $this->createMock(Canonical::class);
        $canonical->expects($this->once())
            ->method('setPath')
            ->with('homepage_locale', [
                '_locale' => 'en',
            ]);

        $this->createContentHelper($canonical)->setCanonicalPath($content, 'en');
    }

    public function testNonContentIsIgnored(): void
    {
        $canonical = $this->createMock(Canonical::class);
        $canonical->expects($this->never())
            ->method('setPath');

        $this->createContentHelper($canonical)->setCanonicalPath(null, 'en');
    }

    private function createContent(
        bool $singleton,
        string $singularSlug,
        string $slug,
        string $recordSlug,
        ?string $recordRoute = null
    ): Content {
        $definition = $this->createMock(ContentType::class);
        $definition->method('get')->willReturnCallback(
            static fn (string $key) => match ($key) {
                'singleton' => $singleton,
                'record_route' => $recordRoute,
                default => null,
            }
        );

        $content = $this->createMock(Content::class);
        $content->method('getDefinition')->willReturn($definition);
        $content->method('getContentTypeSingularSlug')->willReturn($singularSlug);
        $content->method('getContentTypeSlug')->willReturn($slug);
        $content->method('getSlug')->willReturn($recordSlug);
        $content->method('getId')->willReturn(1);

        return $content;
    }

    private function createContentHelper(Canonical $canonical): ContentHelper
    {
        $config = $this->createMock(Config::class);
        // Only `general/homepage` is consulted (via isHomepage()); a content type
        // is the homepage when its slug matches this value.
        $config->method('get')->willReturnCallback(
            static fn (string $path) => $path === 'general/homepage' ? 'homepage' : null
        );

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn(new Request());

        return new ContentHelper(
            $canonical,
            $requestStack,
            $config,
            $this->createMock(LocaleExtension::class)
        );
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Tests\Utils;

use Bolt\Configuration\Content\ContentType;
use Bolt\Repository\ContentRepository;
use Bolt\Utils\ListFormatHelper;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ListFormatHelperTest extends TestCase
{
    private Connection $connection;

    /** @var array{query: string, params: array, types: array}|null */
    private ?array $captured = null;

    private ListFormatHelper $helper;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection
            ->method('fetchAllAssociative')
            ->willReturnCallback(function (string $query, array $params = [], array $types = []): array {
                $this->captured = [
                    'query' => $query,
                    'params' => $params,
                    'types' => $types,
                ];

                return [];
            });

        $this->helper = new ListFormatHelper(
            $this->connection,
            $this->createMock(ContentRepository::class),
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function testGetSelectBindsContentTypesAsParameter(): void
    {
        $this->helper->getSelect('pages,entries', ['order' => 'title', 'limit' => 10]);

        $this->assertStringContainsString('content_type IN (:contentTypes)', $this->captured['query']);
        $this->assertSame(['contentTypes' => ['pages', 'entries']], $this->captured['params']);
        $this->assertSame(['contentTypes' => ArrayParameterType::STRING], $this->captured['types']);
    }

    public function testGetSelectStripsWrappingParentheses(): void
    {
        $this->helper->getSelect('(pages,entries)', ['order' => 'title', 'limit' => 10]);

        $this->assertSame(['pages', 'entries'], $this->captured['params']['contentTypes']);
    }

    /**
     * A malicious content type slug must never end up in the SQL string; it has
     * to be passed through verbatim as a bound parameter value instead.
     */
    public function testGetSelectDoesNotInterpolateMaliciousContentType(): void
    {
        $payload = 'pages" OR "1"="1';

        $this->helper->getSelect($payload, ['order' => 'title', 'limit' => 10]);

        $this->assertStringNotContainsString($payload, $this->captured['query']);
        $this->assertStringNotContainsString('"', $this->captured['query']);
        $this->assertSame([$payload], $this->captured['params']['contentTypes']);
    }

    public function testGetRelatedBindsContentTypeSlug(): void
    {
        $contentType = ContentType::factory('pages', $this->contentTypesConfig());

        $this->helper->getRelated($contentType, 5, 'title');

        $this->assertStringContainsString('content_type = :contentType', $this->captured['query']);
        $this->assertStringNotContainsString('"pages"', $this->captured['query']);
        $this->assertSame(['contentType' => 'pages'], $this->captured['params']);
    }

    public function testGetMenuLinksBindsContentTypeSlug(): void
    {
        $contentType = ContentType::factory('pages', $this->contentTypesConfig());

        $this->helper->getMenuLinks($contentType, 5, 'title');

        $this->assertStringContainsString('content_type = :contentType', $this->captured['query']);
        $this->assertStringNotContainsString('"pages"', $this->captured['query']);
        $this->assertSame(['contentType' => 'pages'], $this->captured['params']);
    }

    private function contentTypesConfig(): \Illuminate\Support\Collection
    {
        return collect([
            'pages' => collect([
                'slug' => 'pages',
                'name' => 'Pages',
                'singular_slug' => 'page',
                'singular_name' => 'Page',
            ]),
        ]);
    }
}

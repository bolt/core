<?php

declare(strict_types=1);

namespace Bolt\Api\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Doctrine\ORM\QueryBuilder;

final class ContentExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void
    {
        if ($resourceClass !== Content::class) {
            return;
        }

        $this->requirePublishedContent($queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = []): void
    {
        if ($resourceClass !== Content::class) {
            return;
        }

        $this->requirePublishedContent($queryBuilder);
        $this->excludeViewlessContent($queryBuilder);
    }

    private function requirePublishedContent(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.status = :status', $rootAlias));
        $queryBuilder->setParameter('status', Statuses::PUBLISHED);
    }

    private function excludeViewlessContent(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $viewlessContentTypes = $this->config->get('contenttypes')->filter(function ($ct) {
            return $ct->get('viewless', false);
        })->map(function ($ct) {
            return $ct->get('slug');
        })->toArray();

        $queryBuilder->andWhere(sprintf('%s.contentType NOT IN (:cts)', $rootAlias));
        $queryBuilder->setParameter('cts', $viewlessContentTypes);
    }
}

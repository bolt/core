<?php

declare(strict_types=1);

namespace Bolt\Api\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Doctrine\ORM\QueryBuilder;

final class ContentExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void
    {
        $this->requirePublishedContent($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = []): void
    {
        $this->requirePublishedContent($queryBuilder, $resourceClass);
    }

    private function requirePublishedContent(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($resourceClass !== Content::class) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.status = :status', $rootAlias));
        $queryBuilder->setParameter('status', Statuses::PUBLISHED);
    }
}

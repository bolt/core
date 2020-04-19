<?php

declare(strict_types=1);

namespace Bolt\Api\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Enum\Statuses;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final class ContentExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /** @var Config */
    private $config;

    private $viewlessContentTypes;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->viewlessContentTypes = $this->config->get('contenttypes')->filter(function ($ct) {
            return $ct->get('viewless', false);
        })->map(function ($ct) {
            return $ct->get('slug');
        })->toArray();
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void
    {
        if ($resourceClass === Content::class) {
            $this->filterUnpublishedViewlessContent($queryBuilder);
        }

        if ($resourceClass === Field::class) {
            $this->filterUnpublishedViewlessFields($queryBuilder);
        }
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = []): void
    {
        if ($resourceClass === Content::class) {
            $this->filterUnpublishedViewlessContent($queryBuilder);
        }

        if ($resourceClass === Field::class) {
            $this->filterUnpublishedViewlessFields($queryBuilder);
        }
    }

    private function filterUnpublishedViewlessContent(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.status = :status', $rootAlias));
        $queryBuilder->andWhere(sprintf('%s.contentType NOT IN (:cts)', $rootAlias));
        $queryBuilder->setParameter('status', Statuses::PUBLISHED);
        $queryBuilder->setParameter('cts', $this->viewlessContentTypes);
    }

    private function filterUnpublishedViewlessFields(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join($rootAlias . '.content', 'c', Join::WITH, 'c.status = :status AND c.contentType NOT IN (:cts)');
        $queryBuilder->setParameter('status', Statuses::PUBLISHED);
        $queryBuilder->setParameter('cts', $this->viewlessContentTypes);
    }
}

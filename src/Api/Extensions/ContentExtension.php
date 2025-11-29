<?php

declare(strict_types=1);

namespace Bolt\Api\Extensions;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Enum\Statuses;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;

final class ContentExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /** @var Collection */
    private $viewlessContentTypes;

    public function __construct(
        private readonly Config $config
    ) {
        $this->viewlessContentTypes = $this->config
            ->get('contenttypes')
            ->filter(fn (Collection $ct) => $ct->get('viewless', false))
            ->map(fn (Collection $ct) => $ct->get('slug'))
            ->values();
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        /*
         * Note: We're not distinguishing between `viewless` and `viewless_listing` here. In the
         * context of the API it makes no sense to say "You can get a list, but not the details"
         * or vice versa.
         */

        if ($resourceClass === Content::class) {
            $this->filterUnpublishedViewlessContent($queryBuilder);
        }

        if ($resourceClass === Field::class) {
            $this->filterUnpublishedViewlessFields($queryBuilder);
        }
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
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
        $queryBuilder->setParameter('status', Statuses::PUBLISHED);

        //todo: Fix this when https://github.com/doctrine/orm/issues/3835 closed.
        if ($this->viewlessContentTypes->isNotEmpty()) {
            $queryBuilder->andWhere(sprintf('%s.contentType NOT IN (:cts)', $rootAlias));
            $queryBuilder->setParameter('cts', $this->viewlessContentTypes);
        }
    }

    private function filterUnpublishedViewlessFields(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->join($rootAlias . '.content', 'c');
        $queryBuilder->andWhere('c.status = :status');

        $queryBuilder->setParameter('status', Statuses::PUBLISHED);

        //todo: Fix this when https://github.com/doctrine/orm/issues/3835 closed.
        if ($this->viewlessContentTypes->isNotEmpty()) {
            $queryBuilder->andWhere('c.contentType NOT IN (:cts)');
            $queryBuilder->setParameter('cts', $this->viewlessContentTypes);
        }
    }
}

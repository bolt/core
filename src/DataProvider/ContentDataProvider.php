<?php

declare(strict_types=1);

namespace Bolt\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use Bolt\Entity\Content;
use Bolt\Model\ContentResponse;
use Bolt\Repository\ContentRepository;

class ContentDataProvider implements ContextAwareCollectionDataProviderInterface, ItemDataProviderInterface
{
    /**
     * @var QueryItemExtensionInterface[]
    */
    private $itemExtensions;

    /**
     * @var QueryCollectionExtensionInterface[]
     */
    private $collectionExtensions;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    public function __construct(ContentRepository $contentRepository, iterable $itemExtensions, iterable $collectionExtensions)
    {
        $this->contentRepository = $contentRepository;
        $this->itemExtensions = $itemExtensions;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Content::class === $resourceClass || ContentResponse::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $queryBuilder = $this->contentRepository->createQueryBuilder('o');
        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            if ($extension instanceof ContextAwareQueryCollectionExtensionInterface) {
                $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            }
            if ($extension instanceof ContextAwareQueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $content = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            } elseif ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                $content = $extension->getResult($queryBuilder);
            }
        }

        foreach ($queryBuilder->getQuery()->getResult() as $content) {
            yield new ContentResponse($content);
        }
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?ContentResponse
    {
        $queryBuilder = $this->contentRepository->createQueryBuilder('o');
        $queryNameGenerator = new QueryNameGenerator();
        $identifiers = ['id' => $id];
        $content = null;

        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);
            if ($extension instanceof ContextAwareQueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $content = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            } elseif ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                $content = $extension->getResult($queryBuilder);
            }
        }

        if ($content === null) {
            $content = $queryBuilder->getQuery()->getOneOrNullResult();
        }

        if ($content === null) {
            return null;
        }

        return new ContentResponse($content);
    }
}
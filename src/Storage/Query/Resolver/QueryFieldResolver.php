<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Resolver;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Storage\Query\Criteria\ContentCriteria;
use Bolt\Storage\Query\Criteria\PublishedCriteria;
use Bolt\Storage\Query\Expression\FilterExpressionBuilder;
use Bolt\Storage\Query\Helper\Query;
use Bolt\Storage\Query\Scope\ScopeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use GraphQL\Type\Definition\ResolveInfo;

class QueryFieldResolver
{
    private $filterExpressionBuilder;
    private $entityManager;

    public function __construct(
        FilterExpressionBuilder $filterExpressionBuilder,
        EntityManagerInterface $entityManager
    ) {
        $this->filterExpressionBuilder = $filterExpressionBuilder;
        $this->entityManager = $entityManager;
    }

    public function resolve(array $args, ResolveInfo $info, string $scope): array
    {
        if ($info->fieldName === 'hello') {
            return $this->helloMessage();
        }

        return $this->contentResolve($args, $info, $scope);
    }

    private function helloMessage(): array
    {
        return [
            'This message will be shown if welcome query works!',
        ];
    }

    private function contentResolve(array $args, ResolveInfo $info, string $scope): array
    {
        $contentTypeAlias = 'c';

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($contentTypeAlias)
            ->from(Field::class, 'bf1')
            ->innerJoin(
                Content::class,
                $contentTypeAlias,
                Join::WITH,
                sprintf('%s.id = %s.content', $contentTypeAlias, 'bf1')
            );

        if (isset($args['filter'])) {
            $expressions = $this->filterExpressionBuilder->build($args['filter']);
            $parameters = $this->filterExpressionBuilder->getParametersValues();
            $aliasCounter = $this->filterExpressionBuilder->getAliasCounter();

            for ($i=2;$i<=$aliasCounter;$i++) {
                $alias = 'bf'.$i;
                $qb->innerJoin(
                    Field::class,
                    $alias,
                    Join::WITH,
                    sprintf('%s.id = %s.content', $contentTypeAlias, $alias)
                );
            }

            $qb->where($expressions)
                ->setParameters($parameters);
        }

        if ($info->fieldName !== 'content') {
            $qb->addCriteria(
                (new ContentCriteria())->getCriteria($info->fieldName, $contentTypeAlias)
            );
        }

        if ($scope === ScopeEnum::FRONT) {
            $qb->addCriteria(
                (new PublishedCriteria())->getCriteria($contentTypeAlias)
            );
        }

        $qb->setMaxResults($args['limit']);
        $qb->groupBy(sprintf('%s.id', $contentTypeAlias));
        $results = $qb->getQuery()->execute();

        return $this->getPreparedResults($results, $info->getFieldSelection());
    }

    private function getPreparedResults(array $results, array $fields): array
    {
        $preparedResults = [];
        /** @var Content $result */
        foreach ($results as $resultKey => $result) {
            $arrayResult = $result->jsonSerialize();
            foreach (array_keys($fields) as $key) {
                if (array_key_exists($key, $arrayResult['fields'])) {
                    $preparedResults[$resultKey][$key] = $arrayResult['fields'][$key];
                }
            }
            foreach (array_keys($arrayResult) as $contentField) {
                $preparedResults[$resultKey][$contentField] = $arrayResult[$contentField];
            }
            $preparedResults[$resultKey] = new \ArrayObject($preparedResults[$resultKey]);
        }

        return $preparedResults;
    }
}

<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Resolver;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Storage\Query\Criteria\ContentCriteria;
use Bolt\Storage\Query\Criteria\PublishedCriteria;
use Bolt\Storage\Query\Expression\FilterExpressionBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use GraphQL\Type\Definition\ResolveInfo;

class QueryFieldResolver
{
    private $filterExpressionBuilder;
    private $entityManager;

    public function __construct(FilterExpressionBuilder $filterExpressionBuilder, EntityManagerInterface $entityManager)
    {
        $this->filterExpressionBuilder = $filterExpressionBuilder;
        $this->entityManager = $entityManager;
    }

    public function resolve(array $args, ResolveInfo $info): array
    {
        if ($info->fieldName === 'hello') {
            return $this->helloMessage();
        }

        return $this->contentResolve($args, $info);
    }

    private function helloMessage(): array
    {
        return [
            'This message will be shown if welcome query works!',
        ];
    }

    private function contentResolve(array $args, ResolveInfo $info): array
    {
        $parameters = [];

        $contentTypeAlias = 'c';

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($contentTypeAlias)
            ->from(Content::class, $contentTypeAlias)
            ->join(Field::class, $info->fieldName, Join::WITH, $info->fieldName.'.content = c.id');

        if (isset($args['filter'])) {
            $expression = $this->filterExpressionBuilder->build($info->fieldName, $args['filter']);
            $parameters += $this->filterExpressionBuilder->getParametersValues();
            $qb->andWhere($expression)
                ->setParameters($parameters);
        }

        $qb->setMaxResults($args['limit']);
        $qb->groupBy($contentTypeAlias.'.id');

        $qb->addCriteria((new ContentCriteria())->getCriteria($info->fieldName, $contentTypeAlias));
        $qb->addCriteria((new PublishedCriteria())->getCriteria());
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
                $preparedResults[$resultKey][$key] = $arrayResult['fields'][$key];
            }
            $preparedResults[$resultKey] = new \ArrayObject($preparedResults[$resultKey]);
        }

        return $preparedResults;
    }
}

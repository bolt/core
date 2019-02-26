<?php


namespace Bolt\Storage\Query\Resolver;

use Bolt\Storage\Query\FilterExpressionBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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
            'This message will be shown if welcome query works!'
        ];
    }

    private function contentResolve(array $args, ResolveInfo $info): array
    {
        $expression = $this->filterExpressionBuilder->build($args['filter']);

        dump($expression);die;

        $qb = new QueryBuilder($this->entityManager);
        $qb->select('*')
            ->from('content', 'c')
            ->where($expression);

        dump($qb->getDQL());die;
        return [];
    }
}
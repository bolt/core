<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Common\Json;
use Bolt\Configuration\Content\ContentType;
use Bolt\Doctrine\Version;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Tightenco\Collect\Support\Collection;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    private $contentColumns = ['id', 'author', 'contentType', 'status', 'createdAt', 'modifiedAt', 'publishedAt', 'depublishedAt'];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Content::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('content');
    }

    public function findForListing(int $page, int $amountPerPage, ?ContentType $contentType = null, bool $onlyPublished = true, string $sortBy = '', string $filter = '', string $taxonomy = ''): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a');

        if ($contentType) {
            $qb->where('content.contentType = :ct')
                ->setParameter('ct', $contentType->getSlug());
        }

        if ($onlyPublished) {
            $qb->andWhere('content.status = :status')
                ->setParameter('status', Statuses::PUBLISHED);
        }

        if (! empty($sortBy) || ! empty($filter)) {
            $qb->addSelect('f')
                ->innerJoin('content.fields', 'f');
        }

        if ($taxonomy) {
            $qb->addSelect('t')
                ->innerJoin('content.taxonomies', 't')
                ->andWhere('slug', ':taxonomySlug')
                ->setParameter('taxonomySlug', $taxonomy);
        }

        [ $order, $direction, $sortByField ] = $this->createSortBy($sortBy, $contentType);

        if (! $sortByField) {
            $qb->orderBy('content.' . $order, $direction);
        } else {
            // @todo Make sorting on a Field work as expected.
            dump('This is not correct');
            // First, create a querybuilder to get the fields that match the Query
            $sortByQB = $this->getQueryBuilder()
                ->select('partial content.{id}');

            $sortByQB->addSelect('f')
                ->innerJoin('content.fields', 'f')
                ->andWhere('f.name = :fieldname')
                ->setParameter('fieldname', $order)
                ->addOrderBy('f.name', $direction);

            // These are the ID's of content we need.
            $ids = array_column($sortByQB->getQuery()->getArrayResult(), 'id');

            $qb->andWhere('content.id IN (:ids)')
                ->setParameter('ids', $ids);
        }

        if ($filter) {
            // First, create a querybuilder to get the fields that match the Query
            $filterQB = $this->getQueryBuilder()
                ->select('partial content.{id}');

            $filterQB->addSelect('f')
                ->innerJoin('content.fields', 'f')
                ->andWhere($filterQB->expr()->like('f.value', ':filterValue'))
                ->setParameter('filterValue', '%' . $filter . '%');

            // These are the ID's of content we need.
            $ids = array_column($filterQB->getQuery()->getArrayResult(), 'id');

            $qb->andWhere('content.id IN (:ids)')
                ->setParameter('ids', $ids);
        }

        return $this->createPaginator($qb->getQuery(), $page, $amountPerPage);
    }

    /**
     * Cobble together the sorting order, and whether or not it's a column in `content` or `fields`.
     */
    private function createSortBy(string $order, Collection $contentType): array
    {
        if (empty($order)) {
            $order = $contentType->get('sort', '');
        }

        if (mb_strpos($order, '-') === 0) {
            $direction = 'DESC';
            $order = mb_substr($order, 1);
        } elseif (mb_strpos($order, ' DESC') !== false) {
            $direction = 'DESC';
            $order = str_replace(' DESC', '', $order);
        } else {
            $order = str_replace(' ASC', '', $order);
            $direction = 'ASC';
        }

        if (\in_array($order, $this->contentColumns, true)) {
            $sortByField = false;
        } else {
            $sortByField = true;
        }

        return [$order, $direction, $sortByField];
    }

    public function findForTaxonomy(int $page, Collection $taxonomy, string $slug, int $amountPerPage, bool $onlyPublished = true): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a');

        $qb->addSelect('t')
            ->innerJoin('content.taxonomies', 't')
            ->andWhere('t.type = :taxonomyslug')
            ->setParameter('taxonomyslug', $taxonomy->get('slug'))
            ->andWhere('t.slug = :slug')
            ->setParameter('slug', $slug);

        if ($onlyPublished) {
            $qb->andWhere('content.status = :status')
                ->setParameter('status', Statuses::PUBLISHED);
        }

        [ $order, $direction, $sortByField ] = $this->createSortBy('', $taxonomy);

        if (! $sortByField) {
            $qb->orderBy('content.' . $order, $direction);
        }

        return $this->createPaginator($qb->getQuery(), $page, $amountPerPage);
    }

    /**
     * @return Content[]
     */
    public function findLatest(?ContentType $contentType = null, int $amount = 6): array
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.modifiedAt', 'DESC');

        if ($contentType) {
            $qb->where('content.contentType = :ct')
                ->setParameter('ct', $contentType->getSlug());
        }

        $qb->setMaxResults($amount);

        return $qb->getQuery()->getResult();
    }

    public function searchNaive(string $searchTerm, int $page, int $amountPerPage, bool $onlyPublished = true): Pagerfanta
    {
        // First, create a querybuilder to get the fields that match the Query
        $qb = $this->getQueryBuilder()
            ->select('partial content.{id}');

        $qb->addSelect('f')
            ->innerJoin('content.fields', 'f')
            ->andWhere($qb->expr()->like('f.value', ':search'))
            ->setParameter('search', '%' . $searchTerm . '%');

        // These are the ID's of content we need.
        $ids = array_column($qb->getQuery()->getArrayResult(), 'id');

        // Next, we'll get the full Content objects, based on ID's
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.modifiedAt', 'DESC');

        if ($onlyPublished) {
            $qb->andWhere('content.status = :status')
                ->setParameter('status', Statuses::PUBLISHED);
        }

        $qb->andWhere('content.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $this->createPaginator($qb->getQuery(), $page, $amountPerPage);
    }

    public function findOneById(int $id): ?Content
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?Content
    {
        $qb = $this->getQueryBuilder();

        // Because Mysql 5.6 and Sqlite handle values in JSON differently, we
        // need to adapt the query.
        if (Version::useJsonFunction($qb)) {
            $where = "JSON_EXTRACT(slug.value, '$[0]')";
        } else {
            $where = 'slug.value';
            $value = Json::json_encode([$slug]);
        }

        return $qb()
            ->innerJoin('content.fields', 'field')
            ->innerJoin(
                \Bolt\Entity\Field\SlugField::class,
                'slug',
                'WITH',
                'field.id = slug.id'
            )
            ->andWhere($where . ' = :slug')
            ->setParameter('slug', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByFieldValue(string $fieldName, $value): ?Content
    {
        $qb = $this->getQueryBuilder();

        // Because Mysql 5.6 and Sqlite handle values in JSON differently, we
        // need to adapt the query.
        if (Version::useJsonFunction($qb)) {
            $where = "JSON_EXTRACT(field.value, '$[0]')";
        } else {
            $where = 'field.value';
            $value = Json::json_encode([$value]);
        }

        return $qb
            ->innerJoin('content.fields', 'field')
            ->andWhere($where . ' = :value')
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createPaginator(Query $query, int $page, int $amountPerPage): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query, true, true));
        $paginator->setMaxPerPage($amountPerPage);
        $paginator->setCurrentPage($page);
        return $paginator;
    }

    public function findAdjacentBy(string $column, string $direction, int $currentValue, ?string $contentType = null): ?Content
    {
        if ($direction === 'next') {
            $order = 'ASC';
            $whereClause = 'content.' . $column .' > :value';
        } else {
            $order = 'DESC';
            $whereClause = 'content.' . $column .' < :value';
        }

        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.' . $column, $order)
            ->where($whereClause)
            ->setParameter('value', $currentValue)
            ->andWhere('content.status = :status')
            ->setParameter('status', Statuses::PUBLISHED)
            ->setMaxResults(1);

        if ($contentType) {
            $qb->andWhere('content.contentType = :contentType')
                ->setParameter('contentType', $contentType);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}

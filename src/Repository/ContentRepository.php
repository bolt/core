<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    private $contentColumns = ['id', 'author', 'contentType', 'status', 'createdAt', 'modifiedAt', 'publishedAt', 'depublishedAt'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('content');
    }

    public function findForListing(int $page, int $amountPerPage, ?ContentType $contentType = null, bool $onlyPublished = true): Pagerfanta
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

        return $this->createPaginator($qb->getQuery(), $page, $amountPerPage);
    }

    public function findForTaxonomy(int $page, string $taxonomyslug, string $slug, int $amountPerPage, bool $onlyPublished = true): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a');

        $qb->addSelect('t')
            ->innerJoin('content.taxonomies', 't')
            ->andWhere('t.type = :taxonomyslug')
            ->setParameter('taxonomyslug', $taxonomyslug)
            ->andWhere('t.slug = :slug')
            ->setParameter('slug', $slug);

        if ($onlyPublished) {
            $qb->andWhere('content.status = :status')
                ->setParameter('status', Statuses::PUBLISHED);
        }

        return $this->createPaginator($qb->getQuery(), $page, $amountPerPage);
    }

    public function findLatest(?ContentType $contentType = null, ?int $amount = null): ?array
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.modifiedAt', 'DESC');

        if ($contentType) {
            $qb->where('content.contentType = :ct')
                ->setParameter('ct', $contentType->getSlug());
        }

        if ($amount !== null) {
            $qb->setMaxResults($amount);
        }
        return $qb->getQuery()->getResult();
    }

    public function searchNaive(string $searchTerm, int $page, int $amountPerPage, bool $onlyPublished = true): Pagerfanta
    {
        // First, create a querybuilder to get the fields that match the Query
        $qb = $this->getQueryBuilder()
            ->select('partial content.{id}');

        $qb->addSelect('f')
            ->innerJoin('content.fields', 'f')
            ->innerJoin('f.translations', 't')
            ->andWhere($qb->expr()->like('t.value', ':search'))
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

        [$where, $slug] = JsonHelper::wrapJsonFunction('translations.value', $slug, $qb);

        return $qb
            ->innerJoin('content.fields', 'field')
            ->innerJoin(
                \Bolt\Entity\Field\SlugField::class,
                'slug',
                'WITH',
                'field.id = slug.id'
            )
            ->innerJoin('field.translations', 'translations')
            ->andWhere($where . ' = :slug')
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByFieldValue(string $fieldName, $value): ?Content
    {
        $qb = $this->getQueryBuilder();

        [$where, $value] = JsonHelper::wrapJsonFunction('translation.value', $value, $qb);

        return $qb
            ->innerJoin('content.fields', 'field')
            ->innerJoin('field.translations', 'translation')
            ->andWhere($where . ' = :value')
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createPaginator(Query $query, int $page, int $amountPerPage): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
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

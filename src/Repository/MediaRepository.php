<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Path;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[] findAll()
 * @method Media[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function findOneByFullFilename(string $fullFileName, string $location = 'files'): ?Media
    {
        $criteria = [
            'location' => $location,
            'path' => Path::getDirectory($fullFileName),
            'filename' => basename($fullFileName),
        ];

        return $this->findOneBy($criteria);
    }
}

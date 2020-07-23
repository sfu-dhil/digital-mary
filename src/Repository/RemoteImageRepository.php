<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RemoteImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RemoteImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method RemoteImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method RemoteImage[]    findAll()
 * @method RemoteImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemoteImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RemoteImage::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('remoteImage')
            ->orderBy('remoteImage.id')
            ->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|RemoteImage[]
     */
    public function typeaheadSearch($q) {
        throw new \RuntimeException("Not implemented yet.");
        $qb = $this->createQueryBuilder('remoteImage');
        $qb->andWhere('remoteImage.column LIKE :q');
        $qb->orderBy('remoteImage.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    
}

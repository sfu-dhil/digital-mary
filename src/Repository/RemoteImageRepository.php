<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\RemoteImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|RemoteImage find($id, $lockMode = null, $lockVersion = null)
 * @method null|RemoteImage findOneBy(array $criteria, array $orderBy = null)
 * @method RemoteImage[] findAll()
 * @method RemoteImage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemoteImageRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, RemoteImage::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('remoteImage')
            ->orderBy('remoteImage.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|RemoteImage[]
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('remoteImage');
        $qb->andWhere('remoteImage.url LIKE :q');
        $qb->orderBy('remoteImage.url', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Query
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('remoteImage');
        $qb->addSelect('MATCH (remoteImage.url, remoteImage.title, remoteImage.description) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}

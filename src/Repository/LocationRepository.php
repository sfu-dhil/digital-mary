<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|Location find($id, $lockMode = null, $lockVersion = null)
 * @method null|Location findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Location::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('location')
            ->orderBy('location.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Location[]
     */
    public function typeaheadSearch($q) {
        $qb = $this->createQueryBuilder('location');
        $qb->andWhere('location.label LIKE :q');
        $qb->orderBy('location.label', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}

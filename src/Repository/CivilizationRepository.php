<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Civilization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Doctrine\Common\Collections\Collection;


/**
 * @method null|Civilization find($id, $lockMode = null, $lockVersion = null)
 * @method null|Civilization findOneBy(array $criteria, array $orderBy = null)
 * @method Civilization[]    findAll()
 * @method Civilization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CivilizationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Civilization::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('civilization')
            ->orderBy('civilization.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Civilization[]|Collection
     */
    public function typeaheadSearch($q) {
        $qb = $this->createQueryBuilder('civilization');
        $qb->andWhere('civilization.label LIKE :q');
        $qb->orderBy('civilization.label', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}

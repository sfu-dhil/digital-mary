<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Period;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Item find($id, $lockMode = null, $lockVersion = null)
 * @method null|Item findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Item::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('item')
            ->leftJoin('item.periodStart', 'p1')
            ->leftJoin('item.periodEnd', 'p2')
            ->orderBy('p1.sortableYear')
            ->addOrderBy('p2.sortableYear')
            ->addOrderBy('item.name')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Item[]
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('item');
        $qb->andWhere('item.name LIKE :q');
        $qb->orderBy('item.name', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Query
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('item');
        $qb->addSelect('MATCH (item.name, item.description, item.inscription, item.translatedInscription) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @return Collection|Item[]
     */
    public function getFeaturedItems(){
        $qb = $this->createQueryBuilder('item');
        $qb->andWhere('SIZE(item.images) > 0');
        $qb->orderBy('item.id','DESC');
        $qb->setMaxResults(5);
        return $qb->getQuery()->execute();
    }

    public function findItemsByPeriod(Period $period) {
        $qb = $this->createQueryBuilder('item');
        $qb->andWhere("item.periodStart <= :period");
        $qb->andWhere(":period <= item.periodEnd");
        $qb->orderBy('item.name', 'ASC');
        $qb->setParameter('period', $period);
        return $qb->getQuery()->execute();
    }

}

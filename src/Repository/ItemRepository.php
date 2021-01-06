<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

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
            ->getQuery();
    }

    /**
     * @param string $q
     *
     * @return Collection|Item[]
     */
    public function typeaheadSearch($q) {
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
        $qb->addSelect('MATCH(item.name,item.description,item.inscription,item.translatedInscription) AGAINST(:q) AS HIDDEN relevance');
        $qb->andHaving('relevance > 0');
        $qb->orderBy('relevance', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @return Collection|Item[]
     */
    public function featuredItemsQuery(){
        $qb = $this->createQueryBuilder('item');
        $qb->where('item.images is not empty');
        $qb->orderBy('item.created','DESC');
        $qb->setMaxResults(5);
        return $qb->getQuery()->execute();
    }
}

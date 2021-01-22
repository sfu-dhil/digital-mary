<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Image find($id, $lockMode = null, $lockVersion = null)
 * @method null|Image findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Image::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('image')
            ->orderBy('image.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Image[]
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('image');
        $qb->andWhere('image.originalName LIKE :q');
        $qb->orderBy('image.originalName', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $q
     *
     * @return Collection|Image[]
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('image');
        $qb->addSelect('MATCH(image.originalName, image.description) AGAINST(:q) AS HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery()->execute();
    }
}

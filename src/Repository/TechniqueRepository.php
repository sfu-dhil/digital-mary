<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Technique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|Technique find($id, $lockMode = null, $lockVersion = null)
 * @method null|Technique findOneBy(array $criteria, array $orderBy = null)
 * @method Technique[]    findAll()
 * @method Technique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechniqueRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Technique::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('technique')
            ->orderBy('technique.id')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Technique[]
     */
    public function typeaheadSearch($q) {
        $qb = $this->createQueryBuilder('technique');
        $qb->andWhere('technique.label LIKE :q');
        $qb->orderBy('technique.label', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}

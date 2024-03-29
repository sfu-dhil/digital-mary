<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Period;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|Period find($id, $lockMode = null, $lockVersion = null)
 * @method null|Period findOneBy(array $criteria, array $orderBy = null)
 * @method Period[] findAll()
 * @method Period[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Period::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('v')->orderBy('v.sortableYear')->getQuery();
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|Location find($id, $lockMode = null, $lockVersion = null)
 * @method null|Location findOneBy(array $criteria, array $orderBy = null)
 * @method Location[] findAll()
 * @method Location[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Location::class);
    }
}

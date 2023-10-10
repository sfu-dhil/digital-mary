<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Technique;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|Technique find($id, $lockMode = null, $lockVersion = null)
 * @method null|Technique findOneBy(array $criteria, array $orderBy = null)
 * @method Technique[] findAll()
 * @method Technique[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechniqueRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Technique::class);
    }
}

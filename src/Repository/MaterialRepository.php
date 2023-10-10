<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Material;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|Material find($id, $lockMode = null, $lockVersion = null)
 * @method null|Material findOneBy(array $criteria, array $orderBy = null)
 * @method Material[] findAll()
 * @method Material[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Material::class);
    }
}

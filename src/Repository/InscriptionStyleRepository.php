<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\InscriptionStyle;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|InscriptionStyle find($id, $lockMode = null, $lockVersion = null)
 * @method null|InscriptionStyle findOneBy(array $criteria, array $orderBy = null)
 * @method InscriptionStyle[] findAll()
 * @method InscriptionStyle[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionStyleRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, InscriptionStyle::class);
    }
}

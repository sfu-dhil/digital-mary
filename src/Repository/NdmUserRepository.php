<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\NdmUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method null|NdmUser find($id, $lockMode = null, $lockVersion = null)
 * @method null|NdmUser findOneBy(array $criteria, array $orderBy = null)
 * @method NdmUser[]    findAll()
 * @method NdmUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NdmUserRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, NdmUser::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('ndmUser')
            ->orderBy('ndmUser.id')
            ->getQuery()
        ;
    }
}

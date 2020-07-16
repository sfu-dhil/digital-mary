<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Epigraphy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;
use RuntimeException;
use Doctrine\Common\Collections\Collection;

/**
 * @method null|Epigraphy find($id, $lockMode = null, $lockVersion = null)
 * @method null|Epigraphy findOneBy(array $criteria, array $orderBy = null)
 * @method Epigraphy[]    findAll()
 * @method Epigraphy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpigraphyRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Epigraphy::class);
    }

}

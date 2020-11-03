<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PeriodRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=PeriodRepository::class)
 */
class Period extends AbstractTerm {

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sortableYear;

    /**
     * There aren't ManyToOne fields in this entity. Use a Repository method
     * instead.
     */

    public function getSortableYear(): ?int
    {
        return $this->sortableYear;
    }

    public function setSortableYear(int $sortableYear): self
    {
        $this->sortableYear = $sortableYear;

        return $this;
    }

}

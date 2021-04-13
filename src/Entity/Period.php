<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\PeriodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=PeriodRepository::class)
 */
class Period extends AbstractTerm
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sortableYear;

    /**
     * Not ORM defined, because the entity relationship is too complex.
     *
     * @var Collection|Item[]
     */
    private $items;

    public function __construct() {
        parent::__construct();
        $this->items = new ArrayCollection();
    }

    /**
     * There aren't ManyToOne fields in this entity. Use a Repository method
     * instead.
     */
    public function getSortableYear() : ?int {
        return $this->sortableYear;
    }

    public function setSortableYear(int $sortableYear) : self {
        $this->sortableYear = $sortableYear;

        return $this;
    }

    public function setItems($items) {
        $this->items = $items;

        return $this;
    }

    public function getItems() {
        return $this->items;
    }
}

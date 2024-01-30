<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PeriodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

#[ORM\Entity(repositoryClass: PeriodRepository::class)]
class Period extends AbstractTerm {
    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $sortableYear;

    /**
     * Not ORM defined, because the entity relationship is too complex.
     *
     * @var Collection<Item>
     */
    private Collection $items;

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

    public function setItems(array|Collection $items) : self {
        if ($items instanceof Collection) {
            $this->items = $items;
        } else {
            $this->items = new ArrayCollection($items);
        }

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems() : Collection {
        return $this->items;
    }
}

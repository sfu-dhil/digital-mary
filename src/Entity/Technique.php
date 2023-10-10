<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TechniqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * A technique used in the construction of an item eg. metalwork, painting, etc.
 */
#[ORM\Entity(repositoryClass: TechniqueRepository::class)]
class Technique extends AbstractTerm {
    /**
     * @var Collection<Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'techniques')]
    private Collection $items;

    public function __construct() {
        parent::__construct();
        $this->items = new ArrayCollection();
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems() : Collection {
        return $this->items;
    }

    public function addItem(Item $item) : self {
        if ( ! $this->items->contains($item)) {
            $this->items[] = $item;
            $item->addTechnique($this);
        }

        return $this;
    }

    public function removeItem(Item $item) : self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->removeTechnique($this);
        }

        return $this;
    }
}

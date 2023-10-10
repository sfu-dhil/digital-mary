<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\InscriptionStyleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * The type of writing or inscription present on the item.
 */
#[ORM\Entity(repositoryClass: InscriptionStyleRepository::class)]
class InscriptionStyle extends AbstractTerm {
    /**
     * @var Collection<Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'inscriptionStyle')]
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
            $item->setInscriptionStyle($this);
        }

        return $this;
    }

    public function removeItem(Item $item) : self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getInscriptionStyle() === $this) {
                $item->setInscriptionStyle(null);
            }
        }

        return $this;
    }
}

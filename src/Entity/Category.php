<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * The type of the object - chalice, wall painting, portrait, etc.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractTerm {
    /**
     * @var Collection<Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'category')]
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
            $item->addCategory($this);
        }

        return $this;
    }

    public function removeItem(Item $item) : self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getCategory()->contains($this)) {
                $item->removeCategory($this);
            }
        }

        return $this;
    }
}

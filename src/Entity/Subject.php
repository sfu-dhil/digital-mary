<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * A subject of a painting eg. Mary, Joseph, baby Jesus, etc.
 */
#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject extends AbstractTerm {
    #[ORM\Column(type: Types::ARRAY, nullable: false)]
    private array $alternateNames;

    /**
     * @var Collection<Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'subjects')]
    private Collection $items;

    public function __construct() {
        parent::__construct();
        $this->alternateNames = [];
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
            $item->addSubject($this);
        }

        return $this;
    }

    public function removeItem(Item $item) : self {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->removeSubject($this);
        }

        return $this;
    }

    public function getAlternateNames() : ?array {
        return $this->alternateNames;
    }

    public function setAlternateNames(array $alternateNames) : self {
        $this->alternateNames = $alternateNames;

        return $this;
    }

    public function addAlternateName(string $alternateName) : self {
        $this->alternateNames[] = $alternateName;

        return $this;
    }
}

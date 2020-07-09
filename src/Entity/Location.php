<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location extends AbstractEntity {
    /**
     * @var Collection|Item[]
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="findSpot")
     */
    private $itemsFound;

    /**
     * @var Collection|Item[]
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="provenance")
     */
    private $itemsProvenanced;

    public function __construct() {
        parent::__construct();
        $this->itemsFound = new ArrayCollection();
        $this->itemsProvenanced = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string {
        // TODO: Implement __toString() method.
    }

    /**
     * @return Collection|Item[]
     */
    public function getItemsFound() : Collection {
        return $this->itemsFound;
    }

    public function addItemsFound(Item $itemsFound) : self {
        if ( ! $this->itemsFound->contains($itemsFound)) {
            $this->itemsFound[] = $itemsFound;
            $itemsFound->setFindSpot($this);
        }

        return $this;
    }

    public function removeItemsFound(Item $itemsFound) : self {
        if ($this->itemsFound->contains($itemsFound)) {
            $this->itemsFound->removeElement($itemsFound);
            // set the owning side to null (unless already changed)
            if ($itemsFound->getFindSpot() === $this) {
                $itemsFound->setFindSpot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItemsProvenanced() : Collection {
        return $this->itemsProvenanced;
    }

    public function addItemsProvenanced(Item $itemsProvenanced) : self {
        if ( ! $this->itemsProvenanced->contains($itemsProvenanced)) {
            $this->itemsProvenanced[] = $itemsProvenanced;
            $itemsProvenanced->setProvenance($this);
        }

        return $this;
    }

    public function removeItemsProvenanced(Item $itemsProvenanced) : self {
        if ($this->itemsProvenanced->contains($itemsProvenanced)) {
            $this->itemsProvenanced->removeElement($itemsProvenanced);
            // set the owning side to null (unless already changed)
            if ($itemsProvenanced->getProvenance() === $this) {
                $itemsProvenanced->setProvenance(null);
            }
        }

        return $this;
    }
}

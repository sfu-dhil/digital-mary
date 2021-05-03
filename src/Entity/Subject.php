<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * A subject of a painting eg. Mary, Joseph, baby Jesus, etc.
 *
 * @ORM\Entity(repositoryClass=SubjectRepository::class)
 */
class Subject extends AbstractTerm {
    /**
     * @var array
     * @ORM\Column(type="array", nullable=false)
     */
    private $alternateNames;

    /**
     * @var Collection|Item[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Item", mappedBy="subjects")
     */
    private $items;

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

    public function addAlternateName($alternateName) {
        $this->alternateNames[] = $alternateName;

        return $this;
    }
}

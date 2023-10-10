<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location extends AbstractTerm {
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $geonameid = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: Types::STRING, length: 2, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::ARRAY, nullable: false)]
    private array $alternateNames;

    /**
     * @var Collection<Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'findspot')]
    private Collection $itemsFound;

    /**
     * @var Collection<Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'provenance')]
    private Collection $itemsProvenanced;

    public function __construct() {
        parent::__construct();
        $this->alternateNames = [];
        $this->itemsFound = new ArrayCollection();
        $this->itemsProvenanced = new ArrayCollection();
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
            $itemsFound->setFindspot($this);
        }

        return $this;
    }

    public function removeItemsFound(Item $itemsFound) : self {
        if ($this->itemsFound->contains($itemsFound)) {
            $this->itemsFound->removeElement($itemsFound);
            // set the owning side to null (unless already changed)
            if ($itemsFound->getFindspot() === $this) {
                $itemsFound->setFindspot(null);
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

    public function getGeonameid() : ?int {
        return $this->geonameid;
    }

    public function setGeonameid(?int $geonameid) : self {
        $this->geonameid = $geonameid;

        return $this;
    }

    public function getLatitude() : ?float {
        return (float) $this->latitude;
    }

    public function setLatitude(?float $latitude) : self {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude() : ?float {
        return (float) $this->longitude;
    }

    public function setLongitude(?float $longitude) : self {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCountry() : ?string {
        return $this->country;
    }

    public function setCountry(?string $country) : self {
        $this->country = $country;

        return $this;
    }

    public function getAlternateNames() : ?array {
        return $this->alternateNames;
    }

    public function setAlternateNames(array $alternateNames) : self {
        $this->alternateNames = $alternateNames;

        return $this;
    }
}

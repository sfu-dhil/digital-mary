<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Index(name: 'person_ft', columns: ['fullname'], flags: ['fulltext'])]
class Person extends AbstractEntity {
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $fullname = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $citationName = null;

    /**
     * @var Collection<int,Contribution>
     */
    #[ORM\OneToMany(targetEntity: Contribution::class, mappedBy: 'person', cascade: ['remove'])]
    private $contributions;

    public function __construct() {
        parent::__construct();
        $this->contributions = new ArrayCollection();
    }

    public function __toString() : string {
        return $this->fullname;
    }

    public function getFullname() : ?string {
        return $this->fullname;
    }

    public function setFullname(string $fullname) : self {
        $this->fullname = $fullname;

        return $this;
    }

    public function getCitationName() : ?string {
        return $this->citationName;
    }

    public function setCitationName(string $citationName) : self {
        $this->citationName = $citationName;

        return $this;
    }

    /**
     * @return Collection<int,Contribution>
     */
    public function getContributions() : Collection {
        return $this->contributions;
    }

    public function addContribution(Contribution $contribution) : self {
        if ( ! $this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setPerson($this);
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution) : self {
        if ($this->contributions->contains($contribution)) {
            $this->contributions->removeElement($contribution);
            // set the owning side to null (unless already changed)
            if ($contribution->getPerson() === $this) {
                $contribution->setPerson(null);
            }
        }

        return $this;
    }
}

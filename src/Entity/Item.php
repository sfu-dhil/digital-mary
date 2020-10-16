<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * @ORM\Table(indexes={
 *      @ORM\Index(columns={"name", "description", "inscription", "translated_inscription"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $inscription;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $translatedInscription;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $location;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $dimensions;

    /**
     * @var string
     * @ORM\Column(name="bibliography", type="text", nullable=true)
     */
    private $references;

    /**
     * A mapping of dates -> initials.
     *
     * @var array
     * @ORM\Column(type="array")
     */
    private $revisions;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $displayYear;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gregorianYear;

    /**
     * @var Category
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="items")
     */
    private $category;

    /**
     * @var Civilization
     * @ORM\ManyToMany(targetEntity="App\Entity\Civilization", inversedBy="items")
     */
    private $civilization;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $civilizationOther;

    /**
     * @var InscriptionStyle
     * @ORM\ManyToOne(targetEntity="App\Entity\InscriptionStyle", inversedBy="items")
     */
    private $inscriptionStyle;

    /**
     * @var Language
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="items")
     */
    private $inscriptionLanguage;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="itemsFound")
     */
    private $findspot;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $findspotOther;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="itemsProvenanced")
     */
    private $provenance;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $provenanceOther;

    /**
     * @var Collection|Image[]
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="item", cascade={"REMOVE"})
     */
    private $images;

    /**
     * @var Collection|Technique[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Technique", inversedBy="items")
     */
    private $techniques;

    /**
     * @var Collection|Material[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Material", inversedBy="items")
     */
    private $materials;

    /**
     * @var Collection|RemoteImage[]
     * @ORM\OneToMany(targetEntity="App\Entity\RemoteImage", mappedBy="item", cascade={"REMOVE"})
     */
    private $remoteImages;

    /**
     * @var Collection|Subject[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Subject", inversedBy="items")
     */
    private $subjects;

    public function __construct() {
        parent::__construct();
        $this->images = new ArrayCollection();
        $this->techniques = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->remoteImages = new ArrayCollection();
        $this->revisions = [];
        $this->category = new ArrayCollection();
        $this->civilization = new ArrayCollection();
        $this->inscriptionLanguage = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string {
        return $this->name;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function setName(string $name) : self {
        $this->name = $name;

        return $this;
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    public function setDescription(?string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getInscription() : ?string {
        return $this->inscription;
    }

    public function setInscription(?string $inscription) : self {
        $this->inscription = $inscription;

        return $this;
    }

    public function getTranslatedInscription() : ?string {
        return $this->translatedInscription;
    }

    public function setTranslatedInscription(?string $translatedInscription) : self {
        $this->translatedInscription = $translatedInscription;

        return $this;
    }

    public function getDimensions() : ?string {
        return $this->dimensions;
    }

    public function setDimensions(?string $dimensions) : self {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getReferences() : ?string {
        return $this->references;
    }

    public function setReferences(?string $references) : self {
        $this->references = $references;

        return $this;
    }

    public function getRevisions() : ?array {
        usort($this->revisions, function ($a, $b) {
            $d = $b['date'] <=> $a['date'];
            if ($d) {
                return $d;
            }

            return $a['initials'] <=> $b['initials'];
        });

        return $this->revisions;
    }

    public function setRevisions(array $revisions) : self {
        $this->revisions = $revisions;

        return $this;
    }

    public function addRevision($date, $initials) : void {
        foreach ($this->revisions as $revision) {
            if ($revision['date'] === $date && $revision['initials'] === $initials) {
                return;
            }
        }
        $this->revisions[] = ['date' => $date, 'initials' => $initials];
    }

    public function getInscriptionStyle() : ?InscriptionStyle {
        return $this->inscriptionStyle;
    }

    public function setInscriptionStyle(?InscriptionStyle $inscriptionStyle) : self {
        $this->inscriptionStyle = $inscriptionStyle;

        return $this;
    }

    public function getFindspot() : ?Location {
        return $this->findspot;
    }

    public function setFindspot(?Location $findspot) : self {
        $this->findspot = $findspot;

        return $this;
    }

    public function getProvenance() : ?Location {
        return $this->provenance;
    }

    public function setProvenance(?Location $provenance) : self {
        $this->provenance = $provenance;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages() : Collection {
        return $this->images;
    }

    public function addImage(Image $image) : self {
        if ( ! $this->images->contains($image)) {
            $this->images[] = $image;
            $image->setItem($this);
        }

        return $this;
    }

    public function removeImage(Image $image) : self {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getItem() === $this) {
                $image->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Technique[]
     */
    public function getTechniques() : Collection {
        return $this->techniques;
    }

    public function addTechnique(Technique $technique) : self {
        if ( ! $this->techniques->contains($technique)) {
            $this->techniques[] = $technique;
        }

        return $this;
    }

    public function removeTechnique(Technique $technique) : self {
        if ($this->techniques->contains($technique)) {
            $this->techniques->removeElement($technique);
        }

        return $this;
    }

    /**
     * @return Collection|Material[]
     */
    public function getMaterials() : Collection {
        return $this->materials;
    }

    public function addMaterial(Material $material) : self {
        if ( ! $this->materials->contains($material)) {
            $this->materials[] = $material;
        }

        return $this;
    }

    public function removeMaterial(Material $material) : self {
        if ($this->materials->contains($material)) {
            $this->materials->removeElement($material);
        }

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getSubjects() : Collection {
        return $this->subjects;
    }

    public function addSubject(Subject $subject) : self {
        if ( ! $this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
        }

        return $this;
    }

    public function removeSubject(Subject $subject) : self {
        if ($this->subjects->contains($subject)) {
            $this->subjects->removeElement($subject);
        }

        return $this;
    }

    /**
     * @return Collection|RemoteImage[]
     */
    public function getRemoteImages() : Collection {
        return $this->remoteImages;
    }

    public function addRemoteImage(RemoteImage $remoteImage) : self {
        if ( ! $this->remoteImages->contains($remoteImage)) {
            $this->remoteImages[] = $remoteImage;
            $remoteImage->setItem($this);
        }

        return $this;
    }

    public function removeRemoteImage(RemoteImage $remoteImage) : self {
        if ($this->remoteImages->contains($remoteImage)) {
            $this->remoteImages->removeElement($remoteImage);
            // set the owning side to null (unless already changed)
            if ($remoteImage->getItem() === $this) {
                $remoteImage->setItem(null);
            }
        }

        return $this;
    }

    public function getCivilizationOther(): ?string
    {
        return $this->civilizationOther;
    }

    public function setCivilizationOther(?string $civilizationOther): self
    {
        $this->civilizationOther = $civilizationOther;

        return $this;
    }

    public function getFindspotOther(): ?string
    {
        return $this->findspotOther;
    }

    public function setFindspotOther(?string $findspotOther): self
    {
        $this->findspotOther = $findspotOther;

        return $this;
    }

    public function getProvenanceOther(): ?string
    {
        return $this->provenanceOther;
    }

    public function setProvenanceOther(?string $provenanceOther): self
    {
        $this->provenanceOther = $provenanceOther;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Civilization[]
     */
    public function getCivilization(): Collection
    {
        return $this->civilization;
    }

    public function addCivilization(Civilization $civilization): self
    {
        if (!$this->civilization->contains($civilization)) {
            $this->civilization[] = $civilization;
        }

        return $this;
    }

    public function removeCivilization(Civilization $civilization): self
    {
        if ($this->civilization->contains($civilization)) {
            $this->civilization->removeElement($civilization);
        }

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getInscriptionLanguage(): Collection
    {
        return $this->inscriptionLanguage;
    }

    public function addInscriptionLanguage(Language $inscriptionLanguage): self
    {
        if (!$this->inscriptionLanguage->contains($inscriptionLanguage)) {
            $this->inscriptionLanguage[] = $inscriptionLanguage;
        }

        return $this;
    }

    public function removeInscriptionLanguage(Language $inscriptionLanguage): self
    {
        if ($this->inscriptionLanguage->contains($inscriptionLanguage)) {
            $this->inscriptionLanguage->removeElement($inscriptionLanguage);
        }

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDisplayYear(): ?string
    {
        return $this->displayYear;
    }

    public function setDisplayYear(?string $displayYear): self
    {
        $this->displayYear = $displayYear;

        return $this;
    }

    public function getGregorianYear(): ?int
    {
        return $this->gregorianYear;
    }

    public function setGregorianYear(?int $gregorianYear): self
    {
        $this->gregorianYear = $gregorianYear;

        return $this;
    }
}

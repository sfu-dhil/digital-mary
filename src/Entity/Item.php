<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

#[ORM\Index(columns: ['name', 'description', 'inscription', 'translated_inscription'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item extends AbstractEntity {
    #[ORM\Column(type: Types::STRING, nullable: false)]
    private ?string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $inscription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $translatedInscription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dimensions = null;

    #[ORM\Column(name: 'bibliography', type: Types::TEXT, nullable: true)]
    private ?string $references = null;

    /**
     * A mapping of dates -> initials.
     */
    #[ORM\Column(type: Types::ARRAY)]
    private array $revisions;

    #[ORM\Column(type: Types::STRING, length: 60, nullable: true)]
    private ?string $displayYear = null;

    #[ORM\ManyToOne(targetEntity: Period::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Period $periodStart = null;

    #[ORM\ManyToOne(targetEntity: Period::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Period $periodEnd = null;

    /**
     * @var Collection<Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'items')]
    private Collection $category;

    /**
     * @var Collection<Civilization>
     */
    #[ORM\ManyToMany(targetEntity: Civilization::class, inversedBy: 'items')]
    private Collection $civilization;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $civilizationOther = null;

    #[ORM\ManyToOne(targetEntity: InscriptionStyle::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?InscriptionStyle $inscriptionStyle = null;

    /**
     * @var Collection<Language>
     */
    #[ORM\ManyToMany(targetEntity: Language::class, inversedBy: 'items')]
    private Collection $inscriptionLanguage;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'itemsFound')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Location $findspot = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $findspotOther = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'itemsProvenanced')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Location $provenance = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $provenanceOther = null;

    /**
     * @var Collection<Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'item', cascade: ['REMOVE'])]
    private Collection $images;

    /**
     * @var Collection<Technique>
     */
    #[ORM\ManyToMany(targetEntity: Technique::class, inversedBy: 'items')]
    private Collection $techniques;

    /**
     * @var Collection<Material>
     */
    #[ORM\ManyToMany(targetEntity: Material::class, inversedBy: 'items')]
    private Collection $materials;

    /**
     * @var Collection<RemoteImage>
     */
    #[ORM\OneToMany(targetEntity: RemoteImage::class, mappedBy: 'item', cascade: ['REMOVE'])]
    private Collection $remoteImages;

    /**
     * @var Collection<Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'items')]
    private Collection $subjects;

    /**
     * @var Collection<Contribution>
     */
    #[ORM\OneToMany(targetEntity: Contribution::class, mappedBy: 'item', cascade: ['REMOVE'])]
    private $contributions;

    public function __construct() {
        parent::__construct();
        $this->images = new ArrayCollection();
        $this->techniques = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->remoteImages = new ArrayCollection();
        $this->revisions = [];
        $this->category = new ArrayCollection();
        $this->civilization = new ArrayCollection();
        $this->inscriptionLanguage = new ArrayCollection();
    }

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
        usort($this->revisions, function ($a, $b) : int {
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

    /**
     * @param DateTimeInterface|string $date
     */
    public function addRevision($date, string $initials) : void {
        if ($date instanceof DateTimeInterface) {
            $date = $date->format('Y-m-d');
        }
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

    public function getCivilizationOther() : ?string {
        return $this->civilizationOther;
    }

    public function setCivilizationOther(?string $civilizationOther) : self {
        $this->civilizationOther = $civilizationOther;

        return $this;
    }

    public function getFindspotOther() : ?string {
        return $this->findspotOther;
    }

    public function setFindspotOther(?string $findspotOther) : self {
        $this->findspotOther = $findspotOther;

        return $this;
    }

    public function getProvenanceOther() : ?string {
        return $this->provenanceOther;
    }

    public function setProvenanceOther(?string $provenanceOther) : self {
        $this->provenanceOther = $provenanceOther;

        return $this;
    }

    /**
     * @return Category[]|Collection
     */
    public function getCategory() : Collection {
        return $this->category;
    }

    public function addCategory(Category $category) : self {
        if ( ! $this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category) : self {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Civilization[]|Collection
     */
    public function getCivilization() : Collection {
        return $this->civilization;
    }

    public function addCivilization(Civilization $civilization) : self {
        if ( ! $this->civilization->contains($civilization)) {
            $this->civilization[] = $civilization;
        }

        return $this;
    }

    public function removeCivilization(Civilization $civilization) : self {
        if ($this->civilization->contains($civilization)) {
            $this->civilization->removeElement($civilization);
        }

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getInscriptionLanguage() : Collection {
        return $this->inscriptionLanguage;
    }

    public function addInscriptionLanguage(Language $inscriptionLanguage) : self {
        if ( ! $this->inscriptionLanguage->contains($inscriptionLanguage)) {
            $this->inscriptionLanguage[] = $inscriptionLanguage;
        }

        return $this;
    }

    public function removeInscriptionLanguage(Language $inscriptionLanguage) : self {
        if ($this->inscriptionLanguage->contains($inscriptionLanguage)) {
            $this->inscriptionLanguage->removeElement($inscriptionLanguage);
        }

        return $this;
    }

    /**
     * @return Collection|Contribution[]
     */
    public function getContributions() : Collection {
        return $this->contributions;
    }

    public function addContribution(Contribution $contribution) : self {
        if ( ! $this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setItem($this);
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution) : self {
        if ($this->contributions->contains($contribution)) {
            $this->contributions->removeElement($contribution);
            // set the owning side to null (unless already changed)
            if ($contribution->getItem() === $this) {
                $contribution->setItem(null);
            }
        }

        return $this;
    }

    public function getLocation() : ?string {
        return $this->location;
    }

    public function setLocation(?string $location) : self {
        $this->location = $location;

        return $this;
    }

    public function getDisplayYear() : ?string {
        return $this->displayYear;
    }

    public function setDisplayYear(?string $displayYear) : self {
        $this->displayYear = $displayYear;

        return $this;
    }

    public function getPeriodStart() : ?Period {
        return $this->periodStart;
    }

    public function setPeriodStart(?Period $periodStart) : self {
        $this->periodStart = $periodStart;

        return $this;
    }

    public function getPeriodEnd() : ?Period {
        return $this->periodEnd;
    }

    public function setPeriodEnd(?Period $periodEnd) : self {
        $this->periodEnd = $periodEnd;

        return $this;
    }
}

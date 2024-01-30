<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Index(columns: ['original_name', 'description'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image extends AbstractEntity {
    private ?File $imageFile = null;

    private ?File $thumbFile = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private ?bool $public;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: false)]
    private ?string $originalName;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: false)]
    private ?string $imagePath;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: false)]
    private ?string $thumbPath;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $imageSize;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $imageWidth;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $imageHeight;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $license = null;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Item $item = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        if ($this->imageFile) {
            return $this->imageFile->getFilename();
        }

        return 'unknown image file.';
    }

    public function getItem() : ?Item {
        return $this->item;
    }

    public function setItem(?Item $item) : self {
        $this->item = $item;

        return $this;
    }

    public function setThumbFile(File $file) : self {
        $this->thumbFile = $file;

        return $this;
    }

    public function getThumbFile() : ?File {
        return $this->thumbFile;
    }

    public function setImageFile(File $file) : self {
        $this->imageFile = $file;

        return $this;
    }

    public function getImageFile() : ?File {
        return $this->imageFile;
    }

    public function getImageSize() : ?int {
        return $this->imageSize;
    }

    public function setImageSize(int $imageSize) : self {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageWidth() : ?int {
        return $this->imageWidth;
    }

    public function setImageWidth(int $imageWidth) : self {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    public function getImageHeight() : ?int {
        return $this->imageHeight;
    }

    public function setImageHeight(int $imageHeight) : self {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    public function setDescription(?string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getAlt() : ?string {
        if ( ! $this->description) {
            return null;
        }

        return html_entity_decode(strip_tags($this->description));
    }

    public function getPublic() : ?bool {
        return $this->public;
    }

    public function setPublic(bool $public) : self {
        $this->public = $public;

        return $this;
    }

    public function getOriginalName() : ?string {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName) : self {
        $this->originalName = $originalName;

        return $this;
    }

    public function getImagePath() : ?string {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath) : self {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getThumbPath() : ?string {
        return $this->thumbPath;
    }

    public function setThumbPath(string $thumbPath) : self {
        $this->thumbPath = $thumbPath;

        return $this;
    }

    public function getLicense() : ?string {
        return $this->license;
    }

    public function setLicense(?string $license) : self {
        $this->license = $license;

        return $this;
    }
}

<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"original_name", "description"}, flags={"fulltext"})
 * })
 */
class Image extends AbstractEntity {
    /**
     * @var ?File
     */
    private $imageFile;

    /**
     * @var ?File
     */
    private $thumbFile;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $public;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $originalName;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $imagePath;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $thumbPath;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $imageSize;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $imageWidth;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $imageHeight;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $license;

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="App\Entity\Item", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    public function __construct() {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
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

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RemoteImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RemoteImageRepository::class)
 */
class RemoteImage extends AbstractEntity {

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Url
     */
    private $url;

    /**
     * Some remote image URLs will point at a webpage containing the image,
     * but some may point at the actual image for embedding purposes.
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isImage;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="remoteImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @inheritDoc
     */
    public function __toString() : string {
        return $this->title;
    }

    public function __construct() {
        parent::__construct();
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

}

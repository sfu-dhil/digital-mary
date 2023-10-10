<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RemoteImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Index(columns: ['url', 'title', 'description'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: RemoteImageRepository::class)]
class RemoteImage extends AbstractEntity {
    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Assert\Url]
    private ?string $url;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    private ?string $title;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'remoteImages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Item $item = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        return $this->title;
    }

    public function getUrl() : ?string {
        return $this->url;
    }

    public function setUrl(string $url) : self {
        $this->url = $url;

        return $this;
    }

    public function getTitle() : ?string {
        return $this->title;
    }

    public function setTitle(string $title) : self {
        $this->title = $title;

        return $this;
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    public function setDescription(string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getItem() : ?Item {
        return $this->item;
    }

    public function setItem(?Item $item) : self {
        $this->item = $item;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use Nines\UtilBundle\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * An abstract term has a computer friendly name, a human readable label,
 * and a description.
 */
#[ORM\Table]
#[ORM\Index(columns: ['label'], flags: ['fulltext'])]
#[ORM\Index(columns: ['description'], flags: ['fulltext'])]
#[ORM\Index(columns: ['label', 'description'], flags: ['fulltext'])]
#[ORM\MappedSuperclass]
abstract class AbstractTerm extends AbstractEntity {
    #[ORM\Column(type: 'string', length: 200)]
    private ?string $label = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        return $this->label;
    }

    public function setLabel(string $label) : self {
        $this->label = $label;

        return $this;
    }

    public function getLabel() : ?string {
        return $this->label;
    }

    public function setDescription(string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getDescription() : ?string {
        return $this->description;
    }
}

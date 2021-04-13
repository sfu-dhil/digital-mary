<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Item;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        $revisions = [
            ['date' => new DateTimeImmutable('2020-03-07'), 'initials' => 'RC'],
            ['date' => new DateTimeImmutable('2020-01-01'), 'initials' => 'MJ'],
            ['date' => new DateTimeImmutable('2020-03-07'), 'initials' => 'MJ'],
        ];

        for ($i = 0; $i < 4; $i++) {
            $fixture = new Item();
            $fixture->setName('Name ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setInscription("<p>This is paragraph {$i}</p>");
            $fixture->setTranslatedInscription("<p>This is paragraph {$i}</p>");
            $fixture->setDimensions('Dimensions ' . $i);
            $fixture->setReferences("<p>This is paragraph {$i}</p>");
            $fixture->setRevisions($revisions);
            $fixture->setDisplayYear(($i + 4) . 'th century');
            $fixture->setPeriodStart($this->getReference('period.' . $i));
            $fixture->setPeriodEnd($this->getReference('period.' . $i));
            $fixture->addCategory($this->getReference('category.' . $i));
            $fixture->addCivilization($this->getReference('civilization.' . $i));
            $fixture->setCivilizationOther('<p>Civilization details</p>');
            $fixture->setInscriptionstyle($this->getReference('inscriptionstyle.' . $i));
            $fixture->setFindspot($this->getReference('location.' . $i));
            $fixture->setFindspotOther('<p>Findspot details</p>');
            $fixture->setProvenance($this->getReference('location.' . $i));
            $fixture->setProvenanceOther('<p>Provenance details</p>');
            $em->persist($fixture);
            $this->setReference('item.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        return [
            CategoryFixtures::class,
            CivilizationFixtures::class,
            InscriptionStyleFixtures::class,
            LocationFixtures::class,
            PeriodFixtures::class,
        ];
    }
}

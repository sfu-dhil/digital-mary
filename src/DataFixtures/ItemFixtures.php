<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\CircaDate;
use App\Entity\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        $data = [
            ['date' => '2020-03-07', 'initials' => 'RC'],
            ['date' => '2020-01-01', 'initials' => 'MJ'],
            ['date' => '2020-03-07', 'initials' => 'MJ'],
        ];

        for ($i = 0; $i < 4; $i++) {
            $fixture = new Item();

            $fixture->setName('New Name ' . $i);
            $fixture->setCircaDate($this->getReference('circadate.' . $i));
            $fixture->setDescription('New Description ' . $i);
            $fixture->setInscription('New Inscription ' . $i);
            $fixture->setTranslatedInscription('New TranslatedInscription ' . $i);
            $fixture->setDimensions('New Dimensions ' . $i);
            $fixture->setReferences('New References ' . $i);
            $fixture->setRevisions($data);

            $fixture->setCategory($this->getReference('category.' . $i));
            $fixture->setCivilization($this->getReference('civilization.' . $i));
            $fixture->setInscriptionStyle($this->getReference('inscriptionStyle.' . $i));
            $fixture->setFindSpot($this->getReference('location.' . $i));
            $fixture->setProvenance($this->getReference('location.' . $i));
            $fixture->addMaterial($this->getReference('material.' . $i));
            $fixture->addSubject($this->getReference('subject.' . $i));
            $fixture->addTechnique($this->getReference('technique.' . $i));

            $em->persist($fixture);
            $this->setReference('item.' . $i, $fixture);
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        // add dependencies here, or remove this
        // function and "implements DependentFixtureInterface" above
        return [
            CategoryFixtures::class,
            CircaDateFixtures::class,
            CivilizationFixtures::class,
            InscriptionStyleFixtures::class,
            LocationFixtures::class,
            MaterialFixtures::class,
            SubjectFixtures::class,
            TechniqueFixtures::class,
        ];
    }
}

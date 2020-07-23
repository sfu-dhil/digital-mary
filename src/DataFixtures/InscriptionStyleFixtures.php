<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\InscriptionStyle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InscriptionStyleFixtures extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new InscriptionStyle();

            $fixture->setName('New Name ' . $i);
            $fixture->setLabel('New Label ' . $i);
            $fixture->setDescription('New Description ' . $i);

            $em->persist($fixture);
            $this->setReference('inscriptionStyle.' . $i, $fixture);
        }

        $em->flush();
    }

}
<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Location();
            $fixture->setName('new location ' . $i);
            $fixture->setLabel('New Location ' . $i);
            $fixture->setDescription('New location description ' . $i);
            $fixture->setLatitude(30.044420 + $i);
            $fixture->setLongitude(31.235712 + $i);

            $em->persist($fixture);
            $this->setReference('location.' . $i, $fixture);
        }

        $em->flush();
    }
}

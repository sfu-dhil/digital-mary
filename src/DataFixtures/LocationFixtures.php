<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Location();
            $fixture->setName('Name ' . $i);
            $fixture->setLabel('Label ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setGeonameid($i);
            $fixture->setLatitude($i + 0.5);
            $fixture->setLongitude($i + 0.5);
            $fixture->setCountry('a' . chr(64 + $i));
            $fixture->setAlternateNames(['AlternateNames ' . $i]);
            $em->persist($fixture);
            $this->setReference('location.' . $i, $fixture);
        }
        $em->flush();
    }
}

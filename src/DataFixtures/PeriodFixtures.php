<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Period;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PeriodFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Period();
            $fixture->setName(($i + 400) . 'th-century');
            $fixture->setLabel(($i + 400) . 'th Century');
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setSortableYear($i + 400);
            $em->persist($fixture);
            $this->setReference('period.' . $i, $fixture);
        }
        $em->flush();
    }
}

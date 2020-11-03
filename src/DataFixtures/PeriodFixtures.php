<?php

namespace App\DataFixtures;

use App\Entity\Period;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PeriodFixtures extends Fixture {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Period();
            $fixture->setName(($i + 400) . 'th-century');
            $fixture->setLabel(($i + 400) . 'th Century');
            $fixture->setDescription("<p>This is paragraph ${i}</p>");
            $fixture->setSortableYear($i + 400);
            $em->persist($fixture);
            $this->setReference('period.' . $i, $fixture);
        }
        $em->flush();
    }
}

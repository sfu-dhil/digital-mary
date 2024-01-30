<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Config\ContributorRole;
use App\Entity\Contribution;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContributionFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 8; $i++) {
            $fixture = new Contribution();
            $fixture->setPerson($this->getReference("person." . ($i % 4)));
            $fixture->setRoles([$i % 2 == 0 ? ContributorRole::aut : ContributorRole::dtm]);
            $fixture->setItem($this->getReference($i < 4 ? 'item.1' : 'item.2'));
            $em->persist($fixture);
            $this->setReference('contribution.' . $i, $fixture);
        }

        $em->flush();
    }

    public function getDependencies() {
        return [
            ItemFixtures::class,
            PersonFixtures::class,
        ];
    }
}

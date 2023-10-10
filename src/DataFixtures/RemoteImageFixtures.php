<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\RemoteImage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RemoteImageFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new RemoteImage();
            $fixture->setUrl('http://example.com/image/' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setItem($this->getReference('item.1'));
            $em->persist($fixture);
            $this->setReference('remoteimage.' . $i, $fixture);
        }
        $em->flush();
    }

    public function getDependencies() {
        return [
            ItemFixtures::class,
        ];
    }
}

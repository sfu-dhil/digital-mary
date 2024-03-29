<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Imagick;
use ImagickPixel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $image = new Imagick();
            $hue = $i * 20;
            $image->newImage(640, 480, new ImagickPixel("hsb({$hue}%, 100%,  75%)"));
            $image->setImageFormat('png');
            $tmp = tmpfile();
            fwrite($tmp, $image->getImageBlob());
            $upload = new UploadedFile(stream_get_meta_data($tmp)['uri'], "image_{$i}.png", 'image/png', null, true);

            $fixture = new Image();
            $fixture->setImageFile($upload);
            $fixture->setPublic(0 === $i % 2);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setLicense("<p>This is paragraph {$i}</p>");
            $fixture->setItem($this->getReference('item.1'));
            $em->persist($fixture);
            $this->setReference('image.' . $i, $fixture);
        }
        $em->flush();
    }

    public function getDependencies() {
        return [
            ItemFixtures::class,
        ];
    }
}

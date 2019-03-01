<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Images
        $images[] = (new Image())->setName('wallpaper.jpg');
        $images[] = (new Image())->setName('wallpaper2.jpg');
        foreach ($images as $id => $image) {
            $this->addReference(Image::class.$id, $image);
            $manager->persist($image);
        }
        $manager->flush();
    }
}

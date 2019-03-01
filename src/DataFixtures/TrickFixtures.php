<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    use ConstructFixtures;

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        $date->format('Y-m-d H:i:s');

        for ($i = 0; $i < 20; $i++) {
            $trick = new Trick();
            $trick->setName($this->faker->text(30));
            $trick->setDescription($this->faker->realText(900));
            $trick->setPublishDate($date);
            $trick->setThumbnail($this->getReference(Image::class.'0'));
            $trick->setTag($this->getReference(Tag::class.'0'));
            //
            $trick->addVideo($this->getReference(Video::class.'0'));
            $trick->addVideo($this->getReference(Video::class.'1'));
            //
            $trick->addImage($this->getReference(Image::class.'0'));
            $trick->addImage($this->getReference(Image::class.'1'));

            $this->addReference(Trick::class.$i, $trick);
            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            VideoFixtures::class,
            ImageFixtures::class,
            TagFixtures::class,
        );
    }
}

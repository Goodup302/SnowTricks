<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\Video;
use App\Service\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    use ConstructFixtures;

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        $date->format('Y-m-d H:i:s');
        for ($i = 0; $i < 50; $i++) {
            //Images
            $images[$i][] = (new Image())->setName("wallpaper.jpg")->setThumbnail(true)->setAlt($this->faker->text(10));
            foreach ($images[$i] as $id => $image) $manager->persist($image);
            //Videos
            $videos[$i][] = (new Video())->setPlatform(Video::YOUTUBE_TYPE)->setVideoId('SQyTWk7OxSI');
            $videos[$i][] = (new Video())->setPlatform(Video::DAILYMOTION_TYPE)->setVideoId('x6evv3');
            foreach ($videos[$i] as $id => $video) $manager->persist($video);
            //Trick
            $trick = new Trick();
            $trick
                ->setName($this->faker->text(30))
                ->setDescription($this->faker->realText(900))
                ->setPublishDate($date)
                ->setTag($this->getReference(Tag::class.'0'))
                //
                ->addVideo($videos[$i][rand(0, 1)])
                ->addImage($images[$i][0])
            ;
            $trick->setSlug(Utils::slugify($trick->getName()));
            $this->addReference(Trick::class.$i, $trick);
            $manager->persist($trick);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            TagFixtures::class,
        );
    }
}

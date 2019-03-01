<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $videos[] = (new Video())
            ->setPlatform(Video::YOUTUBE_TYPE)
            ->setVideoId('ZmEd0MGE4Mo')
        ;
        $videos[] = (new Video())
            ->setPlatform(Video::DAILYMOTION_TYPE)
            ->setVideoId('x72qupe')
        ;
        foreach ($videos as $id => $video) {
            $this->addReference(Video::class.$id, $video);
            $manager->persist($video);
        }
        $manager->flush();
    }
}

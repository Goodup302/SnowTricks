<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tags[] = (new Tag())->setName('Grabs');
        $tags[] = (new Tag())->setName('Rotations');
        $tags[] = (new Tag())->setName('Flips');
        $tags[] = (new Tag())->setName('Rotations désaxées');
        $tags[] = (new Tag())->setName('Slides');
        $tags[] = (new Tag())->setName('One foot tricks');
        $tags[] = (new Tag())->setName('Old school');
        foreach ($tags as $id => $tag) {
            $this->addReference(Tag::class.$id, $tag);
            $manager->persist($tag);
        }
        $manager->flush();
    }
}

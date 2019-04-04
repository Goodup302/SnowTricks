<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    use ConstructFixtures;

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        $date->format('Y-m-d H:i:s');

        for ($i = 0; $i < 2; $i++) {
            for ($ii = 0; $ii < 20; $ii++) {
                $_date = $date->add(new \DateInterval('P1D'));
                $comment = (new Comment())
                    ->setContent("{$ii} - {$this->faker->realText()}")
                    ->setPublishDate($_date)
                    ->setUser($this->getReference(UserFixtures::MAIN_USER))
                    ->setTrick($this->getReference(Trick::class.$i))
                ;
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            TrickFixtures::class,
        );
    }
}

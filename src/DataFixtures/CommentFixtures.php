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
        $date->sub(\DateInterval::createFromDateString('356 day'));

        for ($i = 0; $i < $this->size; $i++) {
            for ($ii = 0; $ii < 23; $ii++) {
                $date = $date->add(\DateInterval::createFromDateString('1 day'));
                $comment = (new Comment())
                    ->setContent("{$this->faker->realText()}")
                    ->setPublishDate($date)
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

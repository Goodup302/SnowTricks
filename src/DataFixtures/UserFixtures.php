<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    use ConstructFixtures;

    public const MAIN_USER = 'main_user';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setUsername($this->faker->name);
            $user->setPassword($this->encoder->encodePassword($user, 'admin'));
            $user->setEmail($this->defaultEmail);
            $user->createToken();
            $manager->persist($user);
        }
        $mainUser = new User();
        $mainUser->setUsername("admin");
        $mainUser->setPassword($this->encoder->encodePassword($user, 'admin'));
        $mainUser->setEmail($this->defaultEmail);
        $mainUser->setActivate(true);
        $mainUser->createToken();
        $this->addReference(self::MAIN_USER, $mainUser);
        $manager->persist($mainUser);

        $manager->flush();
    }
}

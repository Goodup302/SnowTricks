<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    use ConstructFixtures;

    public const MAIN_USER = 'main_user';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($this->faker->name);
            $user->setPassword($this->encoder->encodePassword($user, 'admin'));
            $user->setEmail($this->faker->email);
            $user->createToken();
            $manager->persist($user);
        }
        $mainUser = new User();
        $mainUser->setUsername("admin");
        $mainUser->setPassword($this->encoder->encodePassword($user, 'admin'));
        $mainUser->setEmail($this->faker->email);
        $mainUser->setActivate(true);
        //$mainUser->setProfileImage($this->getReference(ImageFixtures::TEST));
        $mainUser->createToken();
        $this->addReference(self::MAIN_USER, $mainUser);
        $manager->persist($mainUser);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ImageFixtures::class,
        );
    }
}

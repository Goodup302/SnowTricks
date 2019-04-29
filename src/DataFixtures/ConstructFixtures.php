<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

trait ConstructFixtures
{
    /**
     * @var Generator
     */
    private $faker;

    private $size = 15;

    /**
     * UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->encoder = $encoder;
    }
}

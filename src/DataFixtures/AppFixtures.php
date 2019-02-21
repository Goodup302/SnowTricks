<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    /**
     * @var Generator
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        $date->format('Y-m-d H:i:s');

        //Images
        $images[] = (new Image())->setName('wallpaper.jpg');
        $images[] = (new Image())->setName('wallpaper.jpg');
        foreach ($images as $image) {
            $manager->persist($image);
        }
        //Videos
        $videos[] = (new Video())
            ->setPlatform(Video::YOUTUBE_TYPE)
            ->setVideoId('AjjmceaThgo')
        ;
        $videos[] = (new Video())
            ->setPlatform(Video::YOUTUBE_TYPE)
            ->setVideoId('IxxstCcJlsc')
        ;
        foreach ($videos as $video) {
            $manager->persist($video);
        }

        //Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($this->faker->name);
            $user->setPassword($this->encoder->encodePassword($user, 'admin'));
            $user->setEmail($this->faker->email);
            $user->createToken();
            $manager->persist($user);
        }
        $user = new User();
        $user->setUsername("admin");
        $user->setPassword($this->encoder->encodePassword($user, 'admin'));
        $user->setEmail($this->faker->email);
        $user->setActivate(true);
        $user->createToken();
        $manager->persist($user);

        //Tags
        $tags[] = (new Tag())->setName('Grabs');
        $tags[] = (new Tag())->setName('Rotations');
        $tags[] = (new Tag())->setName('Flips');
        $tags[] = (new Tag())->setName('Rotations désaxées');
        $tags[] = (new Tag())->setName('Slides');
        $tags[] = (new Tag())->setName('One foot tricks');
        $tags[] = (new Tag())->setName('Old school');
        foreach ($tags as $tag) {
            //Tricks
            for ($i = 0; $i < 2; $i++) {
                $trick = new Trick();
                $trick->setName($this->faker->text(30));
                $trick->setDescription($this->faker->randomHtml(3,8));
                $trick->setPublishDate($date);
                //
                $trick->addVideo($videos[0]);
                $trick->addVideo($videos[1]);
                //
                $trick->addImage($images[0]);
                $trick->addImage($images[1]);

                //Comments
                for ($i = 0; $i < 2; $i++) {
                    $comment = (new Comment())
                        ->setContent($this->faker->realText())
                        ->setPublishDate($date)
                        ->setUser($user)
                    ;
                    $manager->persist($comment);
                    $trick->addComment($comment);
                }

                /** @var Tag $tag */
                $tag->addTrick($trick);
                $manager->persist($trick);
            }
            $manager->persist($tag);
        }
        $manager->flush();
    }
}

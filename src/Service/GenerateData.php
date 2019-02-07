<?php

namespace App\Service;

use App\Entity\Media;
use App\Entity\Trick;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;

class GenerateData {
    /**
     * @var Generator
     */
    private $faker;
    /**
     * @var TrickRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(TrickRepository $repository, EntityManagerInterface $em) {
        $this->faker = Factory::create('fr_FR');
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * Create figure
     * @param $number
     */
    public function add($number = 20, $delete = false) {
        if ($delete) {
            $this->delete();
        }
        for ($i = 0; $i < $number; $i++) {
            $figure = new Trick();
            $figure->setName($this->faker->text(30));
            $figure->setDescription($this->faker->realText(1000));
            $figure->setTag(0);
/*            $figure->setImages(array());
            $figure->setVideos(array('npb2tsjG9UU', '6q0GGgI3GdQ', '6q0GGgI3GdQ', '6q0GGgI3GdQ', '6q0GGgI3GdQ'));*/

            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $figure->setPublishDate($date);
            //
            $this->em->persist($figure);
        }
        $this->em->flush();
    }

    /**
     * Delete figures
     */
    public function delete() {
        $this->repository->findAll();
        foreach ($this->repository->findAll() as $figure) {
            $this->em->remove($figure);
        }
        $this->em->flush();
    }

    /**
     * @param float|int $min
     * @param float|int $max
     * @return float
     */
    public function randomFloat($min = 0, $max = 1) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
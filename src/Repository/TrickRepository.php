<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Service\Utils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @return Trick[]|null
     */
    public function getAll()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.publishDate IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }
}

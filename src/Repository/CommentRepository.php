<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getPaginate(int $trickid, int $page, int $limit): ?array
    {
        return $this->createQueryBuilder('c')
            ->setFirstResult(($page-1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('c.publishDate', 'ASC')
            ->join('c.trick', 't', 'WITH', 't.id = :trickid')
            ->setParameter('trickid', $trickid)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int $trickid
     * @return int
     */
    public function countByTrick(int $trickid): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c)')
            ->join('c.trick', 't', 'WITH', 't.id = :trickid')
            ->setParameter('trickid', $trickid)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}

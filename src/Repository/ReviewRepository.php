<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getHightRatedReview()
    {
        return $query = $this
            ->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.rating > 3')
            ->getQuery()
            ->getScalarResult();
    }

    public function randomReview($nbRand)
    {
        return $query = $this
            ->createQueryBuilder('r')
            ->where('r.id = :nbRand')
            ->setParameter('nbRand', $nbRand)
            ->getQuery()
            ->getResult();
    }
}

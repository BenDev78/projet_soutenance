<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function countReviews($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT COUNT(r.rating)
            FROM product p, review r
            WHERE p.id = r.product_id
            AND p.id = ?
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchNumeric();
    }

    public function findSearch($search)
    {
        $query =  $this
            ->createQueryBuilder('p')
        ;

        if(!empty($search->q))
        {
            $query = $query
                ->andWhere('p.name like :q')
                ->setParameter('q', "%{$search->q}%");
        }

        return $query->getQuery()->getResult();
    }

//SELECT p.id, p.name, ROUND(AVG(r.rating), 1) as rate
//FROM product p, review r
//WHERE p.id = r.product_id 
//GROUP BY p.;
//
//SELECT id
//FROM avg_rating
//WHERE rate BETWEEN 3 AND 3 +1;

}

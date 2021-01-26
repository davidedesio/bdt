<?php

namespace App\Repository;

use App\Entity\ActivityMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActivityMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityMatch[]    findAll()
 * @method ActivityMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityMatch::class);
    }

    // /**
    //  * @return ActivityMatch[] Returns an array of ActivityMatch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivityMatch
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

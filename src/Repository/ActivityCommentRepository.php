<?php

namespace App\Repository;

use App\Entity\ActivityComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActivityComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityComment[]    findAll()
 * @method ActivityComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityComment::class);
    }

    // /**
    //  * @return ActivityComment[] Returns an array of ActivityComment objects
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
    public function findOneBySomeField($value): ?ActivityComment
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function fetchSince($activity,$sinceCommentId,$user)
    {
        $comments = $this->createQueryBuilder('a')
            ->andWhere('a.activity = :activity')
            ->setParameter('activity', $activity)
            ->andWhere('a.id > :id')
            ->setParameter('id', $sinceCommentId)
            ->andWhere('a.createUser != :user')
            ->setParameter('user', $user)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;

        if(count($comments)>0){
            $sinceCommentId = $comments[count($comments)-1]->getId();
        }

        return [$comments,$sinceCommentId];
    }
}

<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function table($filters,$orderBy,$limit,$offset){
        $query = $this->createQueryBuilder('t')
            ->leftJoin("t.userTo","toUser")
            ->leftJoin("t.userFrom","fromUser");

        if(array_key_exists("date_from",$filters)) {
            $var = $filters["date_from"];
            $date = str_replace('/', '-', $var);
            $filters["date_from"] = date('Y-m-d', strtotime($date));
            $query->andWhere("t.createTimestamp >= '".$filters["date_from"]."'"); //activity should be in the future
        }

        if(array_key_exists("date_to",$filters)){
            $var = $filters["date_to"];
            $date = str_replace('/', '-', $var);
            $filters["date_to"] = date('Y-m-d', strtotime($date));
            $query->andWhere("t.createTimestamp <= '".$filters["date_to"]."'"); //activity should be in the future
        }

        if(array_key_exists("surname",$filters)) {
            $query
                ->andWhere("( toUser.surname like '%".$filters["surname"]."%' OR fromUser.surname like '%".$filters["surname"]."%' )")
            ;
        }

        return $query->orderBy("t.".$orderBy[0],$orderBy[1])
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countTable($filters){
        $query = $this->createQueryBuilder('t')
            ->select("count(t.id)")
            ->leftJoin("t.userTo","toUser")
            ->leftJoin("t.userFrom","fromUser");

        if(array_key_exists("date_from",$filters)) {
            $var = $filters["date_from"];
            $date = str_replace('/', '-', $var);
            $filters["date_from"] = date('Y-m-d', strtotime($date));
            $query->andWhere("t.createTimestamp >= '".$filters["date_from"]."'"); //activity should be in the future
        }

        if(array_key_exists("date_to",$filters)){
            $var = $filters["date_to"];
            $date = str_replace('/', '-', $var);
            $filters["date_to"] = date('Y-m-d', strtotime($date));
            $query->andWhere("t.createTimestamp <= '".$filters["date_to"]."'"); //activity should be in the future
        }

        if(array_key_exists("surname",$filters)) {
            $query
                ->andWhere("( toUser.surname like '%".$filters["surname"]."%' OR fromUser.surname like '%".$filters["surname"]."%' )")
            ;
        }

        return $query->getQuery()
            ->getSingleScalarResult();
    }


    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function table($filters,$orderBy,$limit,$offset){
        $query = $this->createQueryBuilder('a')
            ->leftJoin("a.createUser","cr")
            ->leftJoin("a.acceptedUser","ac");

        if(array_key_exists("date_from",$filters)) {
            $var = $filters["date_from"];
            $date = str_replace('/', '-', $var);
            $filters["date_from"] = date('Y-m-d', strtotime($date));
            $query->andWhere("a.date >= '".$filters["date_from"]."'"); //activity should be in the future
        } else {
            $query->andWhere("a.date >= CURRENT_DATE()"); //activity should be in the future
        }

        if(array_key_exists("date_to",$filters)) {
            $var = $filters["date_to"];
            $date = str_replace('/', '-', $var);
            $filters["date_to"] = date('Y-m-d', strtotime($date));
            $query->andWhere("a.date <= '".$filters["date_to"]."'"); //activity should be in the future
        }

        if(array_key_exists("acceptedUser",$filters)) {
            $query->andWhere("a.acceptedUser ".$filters["acceptedUser"]);
        }

        if(array_key_exists("surname",$filters)) {
            $query->andWhere("( cr.surname like '%".$filters["surname"]."%' OR ac.surname like '%".$filters["surname"]."%' )");
        }

        return $query->orderBy("a.".$orderBy[0],$orderBy[1])
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countTable($filters){
        $query = $this->createQueryBuilder('a')
            ->select("count(a.id)")
            ->leftJoin("a.createUser","cr")
            ->leftJoin("a.acceptedUser","ac");

        if(array_key_exists("date_from",$filters)) {
            $var = $filters["date_from"];
            $date = str_replace('/', '-', $var);
            $filters["date_from"] = date('Y-m-d', strtotime($date));
            $query->andWhere("a.date >= '".$filters["date_from"]."'"); //activity should be in the future
        } else {
            $query->andWhere("a.date >= CURRENT_DATE()"); //activity should be in the future
        }

        if(array_key_exists("date_to",$filters)){
            $var = $filters["date_to"];
            $date = str_replace('/', '-', $var);
            $filters["date_to"] = date('Y-m-d', strtotime($date));
            $query->andWhere("a.date <= '".$filters["date_to"]."'"); //activity should be in the future
        }

        if(array_key_exists("acceptedUser",$filters)) {
            $query->andWhere("a.acceptedUser ".$filters["acceptedUser"]);
        }

        if(array_key_exists("surname",$filters)) {
            $query->andWhere("( cr.surname like '%".$filters["surname"]."%' OR ac.surname like '%".$filters["surname"]."%' )");
        }

        return $query->getQuery()
            ->getSingleScalarResult();
    }

    public function search($filters,User $logged, $offset, $limit)
    {
         $query = $this->createQueryBuilder('a')
             ->andWhere("a.date>=CURRENT_DATE()") //activity should be in the future
             ->andWhere("a.acceptedUser is null"); //show only pending activities

        if(!is_null($filters["type"])){
            $activitytTypeFilter = $filters["type"];
            //apply activity filter
            $query->andWhere('a.type = :type')->setParameter('type', $activitytTypeFilter);
        }

        if(!is_null($filters["category"])){
            $activityCategoryFilter = $filters["category"];
            //apply activity filter
            $query->andWhere('a.category = :category')->setParameter('category', $activityCategoryFilter);
        }

        if(!is_null($filters["user"]) && ($filters["user"]==1 || $filters["user"]==2)){
            $userFilter = $filters["user"];
            //apply user filter
            $operator = "=";
            if($filters["user"]==1){
                $operator = "=";
            } else if($userFilter==2){
                $operator = "!=";
            }
            $query->andWhere("a.createUser $operator :createUser")->setParameter('createUser', $logged);
        }

        if(!is_null($filters["user"]) && $filters["user"]==3){
            $query->leftJoin("a.activityMatches", "m");
            $query->andWhere("m.createUser = :createUser")->setParameter('createUser', $logged);
        }

        return $query->orderBy('a.createTimestamp', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countSearch($filters, User $logged)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere("a.date>=CURRENT_DATE()") //activity should be in the future
            ->select("count(a.id)")
            ->andWhere("a.acceptedUser is null"); //show only pending activities

        if(!is_null($filters["type"])){
            $activitytTypeFilter = $filters["type"];
            //apply activity filter
            $query->andWhere('a.type = :type')->setParameter('type', $activitytTypeFilter);
        }

        if(!is_null($filters["category"])){
            $activityCategoryFilter = $filters["category"];
            //apply activity filter
            $query->andWhere('a.category = :category')->setParameter('category', $activityCategoryFilter);
        }

        if(!is_null($filters["user"]) && ($filters["user"]==1 || $filters["user"]==2)){
            $userFilter = $filters["user"];
            //apply user filter
            $operator = "=";
            if($filters["user"]==1){
                $operator = "=";
            } else if($userFilter==2){
                $operator = "!=";
            }
            $query->andWhere("a.createUser $operator :createUser")->setParameter('createUser', $logged);
        }

        if(!is_null($filters["user"]) && $filters["user"]==3){
            $query->leftJoin("a.activityMatches", "m");
            $query->andWhere("m.createUser = :createUser")->setParameter('createUser', $logged);
        }

        return $query->orderBy('a.createTimestamp', 'DESC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findForTimeline(User $user, $offset, $limit){
        $query = $this->createQueryBuilder('a')
            ->andWhere("a.acceptedUser is not null") //show only confirmed activities
            ->andWhere("a.acceptedUser = :user or a.createUser = :user")
            ->setParameter('user', $user);

        return $query->orderBy('a.date', 'DESC')->addOrderBy('a.time','DESC') //order from newers to olders
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countForTimeline(User $user){
        $query = $this->createQueryBuilder('a')
            ->select("count(a.id)")
            ->andWhere("a.acceptedUser is not null") //show only confirmed activities
            ->andWhere("a.acceptedUser = :user or a.createUser = :user")
            ->setParameter('user', $user);

        return $query->orderBy('a.date', 'DESC')->addOrderBy('a.time','DESC') //order from newers to olders
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findForCalendar(User $user, $dates){
        $query = $this->createQueryBuilder('a')
            ->andWhere("a.acceptedUser = :user or a.createUser = :user")->setParameter('user', $user)
            ->andWhere("a.date >= '".($dates["start"]->format("Y-m-d"))."' and a.date <= '".($dates["end"]->format("Y-m-d")."'"));

        return $query->orderBy('a.date', 'ASC')->addOrderBy('a.time','ASC')
            ->getQuery()
            ->getResult();
    }

    public function findForAdminCalendar($filters){
        $query = $this->createQueryBuilder('a')
            ->andWhere("a.date >= '".($filters["start"]->format("Y-m-d"))."' and a.date <= '".($filters["end"]->format("Y-m-d")."'"));

        if(!is_null($filters["activityTypeId"])){
            $activityTypeId = $filters["activityTypeId"];
            $query->leftJoin("a.type", "t")
                ->andWhere("t.id = ".$activityTypeId);
        }
        if(!is_null($filters["activityCategoryId"])){
            $activityCategoryId = $filters["activityCategoryId"];
            $query->leftJoin("a.category", "c")
                ->andWhere("c.id = ".$activityCategoryId);
        }

        if(!is_null($filters["accepted"]) && !empty($filters["accepted"])){
            $accepted = $filters["accepted"];
            if($accepted=="yes"){
                $query->andWhere("a.acceptedUser is not null");
            } else if ($accepted=="no"){
                $query->andWhere("a.acceptedUser is null");
            }

        }

        return $query->orderBy('a.date', 'ASC')->addOrderBy('a.time','ASC')
            ->getQuery()
            ->getResult();
    }

    public function next(User $user)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere("a.date>=CURRENT_DATE()") //activity should be in the future
            ->andWhere("a.acceptedUser is not null")
            ->andWhere("a.createUser = :user or a.acceptedUser = :user")->setParameter('user', $user);
        return $query->orderBy('a.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}

<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    /* @return User[] Returns an array of User objects */
    public function getLeaderboardPoints()
    {
        $query = $this->createQueryBuilder('u')
            ->select("u.id,u.name,u.surname")
            ->addSelect("
                (SELECT count(r.id)*10 FROM App\\Entity\\Transaction r WHERE r.userTo=u.id and r.userFrom is not null) as receivedPoints,
                (SELECT count(s.id)*5 FROM App\\Entity\\Transaction s WHERE s.userFrom=u.id) as sentPoints
                "
            )
            ->where("u._del = 0")
            ->having("SUM(receivedPoints+sentPoints)>0")
            ->groupBy("u.id")
            ->orderBy("SUM(receivedPoints+sentPoints)","DESC");

        $result = $query->getQuery()
            ->setMaxResults(10)
            ->getResult()
        ;

        return $result;
    }

    /* @return User[] Returns an array of User objects */
    public function getLeaderboardDone()
    {
        $query = $this->createQueryBuilder('u')
            ->select("u.id,u.name,u.surname")
            ->addSelect("(SELECT count(r.id) FROM App\\Entity\\Transaction r WHERE r.userTo=u.id and r.userFrom is not null) as done")
            ->where("u._del = 0")
            ->having("done>0")
            ->groupBy("u.id")
            ->orderBy("done","DESC");

        $result = $query->getQuery()
            ->setMaxResults(10)
            ->getResult()
        ;

        return $result;
    }

    /* @return User[] Returns an array of User objects */
    public function getLeaderboardGet()
    {
        $query = $this->createQueryBuilder('u')
            ->select("u.id,u.name,u.surname")
            ->addSelect("(SELECT count(s.id) FROM App\\Entity\\Transaction s WHERE s.userFrom=u.id) as get")
            ->where("u._del = 0")
            ->having("get>0")
            ->groupBy("u.id")
            ->orderBy("get","DESC");

        $result = $query->getQuery()
            ->setMaxResults(10)
            ->getResult()
        ;

        return $result;
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

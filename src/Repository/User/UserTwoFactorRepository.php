<?php

namespace App\Repository\User;

use App\Entity\User\UserTwoFactor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserTwoFactor|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTwoFactor|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTwoFactor[]    findAll()
 * @method UserTwoFactor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTwoFactorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTwoFactor::class);
    }

    // /**
    //  * @return UserTwoFactor[] Returns an array of UserTwoFactor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserTwoFactor
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

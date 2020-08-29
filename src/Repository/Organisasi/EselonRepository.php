<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Eselon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Eselon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eselon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eselon[]    findAll()
 * @method Eselon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EselonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eselon::class);
    }

    // /**
    //  * @return Eselon[] Returns an array of Eselon objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Eselon
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

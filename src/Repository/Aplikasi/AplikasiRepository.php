<?php

namespace App\Repository\Aplikasi;

use App\Entity\Aplikasi\Aplikasi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Aplikasi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aplikasi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aplikasi[]    findAll()
 * @method Aplikasi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AplikasiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aplikasi::class);
    }

    // /**
    //  * @return Aplikasi[] Returns an array of Aplikasi objects
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
    public function findOneBySomeField($value): ?Aplikasi
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

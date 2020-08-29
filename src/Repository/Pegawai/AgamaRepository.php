<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\Agama;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agama|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agama|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agama[]    findAll()
 * @method Agama[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgamaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agama::class);
    }

    // /**
    //  * @return Agama[] Returns an array of Agama objects
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
    public function findOneBySomeField($value): ?Agama
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

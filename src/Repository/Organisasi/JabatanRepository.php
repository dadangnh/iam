<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Jabatan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jabatan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jabatan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jabatan[]    findAll()
 * @method Jabatan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jabatan::class);
    }

    // /**
    //  * @return Jabatan[] Returns an array of Jabatan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Jabatan
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

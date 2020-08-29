<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\JabatanPegawai;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JabatanPegawai|null find($id, $lockMode = null, $lockVersion = null)
 * @method JabatanPegawai|null findOneBy(array $criteria, array $orderBy = null)
 * @method JabatanPegawai[]    findAll()
 * @method JabatanPegawai[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanPegawaiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JabatanPegawai::class);
    }

    // /**
    //  * @return JabatanPegawai[] Returns an array of JabatanPegawai objects
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
    public function findOneBySomeField($value): ?JabatanPegawai
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

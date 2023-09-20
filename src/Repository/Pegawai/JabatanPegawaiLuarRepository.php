<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\JabatanPegawaiLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JabatanPegawaiLuar>
 *
 * @method JabatanPegawaiLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method JabatanPegawaiLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method JabatanPegawaiLuar[]    findAll()
 * @method JabatanPegawaiLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanPegawaiLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JabatanPegawaiLuar::class);
    }

//    /**
//     * @return JabatanPegawaiLuar[] Returns an array of JabatanPegawaiLuar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JabatanPegawaiLuar
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

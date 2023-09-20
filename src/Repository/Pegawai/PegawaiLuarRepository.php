<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\PegawaiLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PegawaiLuar>
 *
 * @method PegawaiLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method PegawaiLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method PegawaiLuar[]    findAll()
 * @method PegawaiLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PegawaiLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PegawaiLuar::class);
    }

//    /**
//     * @return PegawaiLuar[] Returns an array of PegawaiLuar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PegawaiLuar
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\JabatanLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JabatanLuar>
 *
 * @method JabatanLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method JabatanLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method JabatanLuar[]    findAll()
 * @method JabatanLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JabatanLuar::class);
    }

//    /**
//     * @return JabatanLuar[] Returns an array of JabatanLuar objects
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

//    public function findOneBySomeField($value): ?JabatanLuar
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

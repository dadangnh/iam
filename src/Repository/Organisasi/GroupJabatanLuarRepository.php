<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\GroupJabatanLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupJabatanLuar>
 *
 * @method GroupJabatanLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupJabatanLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupJabatanLuar[]    findAll()
 * @method GroupJabatanLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupJabatanLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupJabatanLuar::class);
    }

//    /**
//     * @return GroupJabatanLuar[] Returns an array of GroupJabatanLuar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupJabatanLuar
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

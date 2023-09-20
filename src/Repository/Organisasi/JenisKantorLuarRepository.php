<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\JenisKantorLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JenisKantorLuar>
 *
 * @method JenisKantorLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method JenisKantorLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method JenisKantorLuar[]    findAll()
 * @method JenisKantorLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JenisKantorLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JenisKantorLuar::class);
    }

//    /**
//     * @return JenisKantorLuar[] Returns an array of JenisKantorLuar objects
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

//    public function findOneBySomeField($value): ?JenisKantorLuar
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

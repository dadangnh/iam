<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\JenisKantor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JenisKantor|null find($id, $lockMode = null, $lockVersion = null)
 * @method JenisKantor|null findOneBy(array $criteria, array $orderBy = null)
 * @method JenisKantor[]    findAll()
 * @method JenisKantor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JenisKantorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JenisKantor::class);
    }

    // /**
    //  * @return JenisKantor[] Returns an array of JenisKantor objects
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
    public function findOneBySomeField($value): ?JenisKantor
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

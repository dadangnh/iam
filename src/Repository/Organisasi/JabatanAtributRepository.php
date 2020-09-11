<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\JabatanAtribut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JabatanAtribut|null find($id, $lockMode = null, $lockVersion = null)
 * @method JabatanAtribut|null findOneBy(array $criteria, array $orderBy = null)
 * @method JabatanAtribut[]    findAll()
 * @method JabatanAtribut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanAtributRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JabatanAtribut::class);
    }

    // /**
    //  * @return JabatanAtribut[] Returns an array of JabatanAtribut objects
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
    public function findOneBySomeField($value): ?JabatanAtribut
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

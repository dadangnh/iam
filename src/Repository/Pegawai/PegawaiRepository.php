<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\Pegawai;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pegawai|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pegawai|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pegawai[]    findAll()
 * @method Pegawai[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PegawaiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pegawai::class);
    }

    public function getPegawaisFromArrayOfUuid(array $ids)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id in (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}

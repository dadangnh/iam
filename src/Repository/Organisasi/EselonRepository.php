<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Eselon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Eselon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eselon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eselon[]    findAll()
 * @method Eselon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EselonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eselon::class);
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function findLegacyDataFromArrayOfIds($ids): mixed
    {
        return $this->createQueryBuilder('e')
            ->select(['e.id', 'e.nama', 'e.tingkat', 'e.kode', 'e.legacyKode'])
            ->andWhere('e.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->addOrderBy('e.tingkat', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

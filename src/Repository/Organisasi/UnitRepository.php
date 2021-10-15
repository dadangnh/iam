<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Unit;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Unit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unit[]    findAll()
 * @method Unit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unit::class);
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findUnitByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('u')
            ->andWhere('lower(u.nama) LIKE :val')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->addOrderBy('u.level', 'ASC')
            ->addOrderBy('u.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAllActiveUnit(): mixed
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.tanggalAktif < :now')
            ->andWhere('u.tanggalNonaktif is null or u.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('u.level', 'ASC')
            ->addOrderBy('u.nama', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findActiveUnitByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('u')
            ->andWhere('lower(u.nama) LIKE :val')
            ->andWhere('u.tanggalAktif < :now')
            ->andWhere('u.tanggalNonaktif is null or u.tanggalNonaktif > :now')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('u.level', 'ASC')
            ->addOrderBy('u.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findLegacyDataFromArrayOfIds($ids)
    {
        return $this->createQueryBuilder('u')
            ->select([
                'u.id',
                'u.nama',
                'u.level',
                'u.legacyKode'
            ])
            ->andWhere('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->addOrderBy('u.level', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

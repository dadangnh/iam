<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Unit;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    public function findAllActiveUnitData(): mixed
    {
        // Fetch parent entities with necessary fields
        $unitAll = $this->createQueryBuilder('unit')
            ->select([
                'unit.id', 'unit.nama', 'unit.level', 'unit.tanggalAktif', 'unit.tanggalNonaktif',
                'unit.legacyKode', 'unit.namaEng',
                'jenisKantor.id AS jenisKantorId', 'parent.id AS parentId', 'eselon.id AS eselonId', 'pembina.id AS pembinaId',
            ])
            ->leftJoin('unit.jenisKantor', 'jenisKantor')
            ->leftJoin('unit.parent', 'parent')
            ->leftJoin('unit.eselon', 'eselon')
            ->leftJoin('unit.pembina', 'pembina')
            ->andWhere('unit.tanggalAktif < :now')
            ->andWhere('unit.tanggalNonaktif IS NULL OR unit.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('unit.level', 'ASC')
            ->addOrderBy('unit.nama', 'ASC');

        $parentUnits = $unitAll->getQuery()->getArrayResult();

        $parentIds = array_map(fn($unit) => (string) $unit['id'], $parentUnits);

        $childEntities = $this->createQueryBuilder('c')
            ->select('c.id, parent.id AS parentId')
            ->leftJoin('c.parent', 'parent')
            ->where('parent.id IN (:parentIds)')
            ->setParameter('parentIds', $parentIds)
            ->getQuery()
            ->getArrayResult();

        $membinaEntities = $this->createQueryBuilder('m')
            ->select('m.id, pembina.id AS pembinaId')
            ->leftJoin('m.pembina', 'pembina')
            ->where('pembina.id IN (:parentIds)')
            ->setParameter('parentIds', $parentIds)
            ->getQuery()
            ->getArrayResult();

        $childrenByParent = [];
        foreach ($childEntities as $child) {
            $parentId = (string) $child['parentId'];
            if (!isset($childrenByParent[$parentId])) {
                $childrenByParent[$parentId] = [];
            }
            $childrenByParent[$parentId][] = (string) ('/api/units/' . $child['id']);

        }

        $membinaByPembina = [];
        foreach ($membinaEntities as $membina) {
            $pembinaId = (string) $membina['pembinaId'];
            if (!isset($membinaByPembina[$pembinaId])) {
                $membinaByPembina[$pembinaId] = [];
            }
            $membinaByPembina[$pembinaId][] = (string) ('/api/units/' . $membina['id']);
        }

        $result = [];
        foreach ($parentUnits as $parent) {
            $parentId = (string) $parent['id'];  // Ensure parent ID is a string
            $result[] = [
                'id' => $parentId,
                'nama' => $parent['nama'],
                'level' => $parent['level'],
                'jenisKantor' => !empty($parent['jenisKantorId']) ? ('/api/jenis_kantors/' . $parent['jenisKantorId']) : null,
                'parent' => !empty($parent['parentId']) ? ('/api/units/' . $parent['parentId']) : null,
                'childs' => $childrenByParent[$parentId] ?? [],
                'tanggalAktif' => $parent['tanggalAktif'],
                'tanggalNonaktif' => $parent['tanggalNonaktif'],
                'legacyKode' => $parent['legacyKode'],
                'pembina' => !empty($parent['pembinaId']) ? ('/api/units/' . $parent['pembinaId']) : null,
                'membina' => $membinaByPembina[$parentId] ?? [],
                'namaEng' => $parent['namaEng'] ?? null
            ];
        }
        return $result;
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

    /**
     * @throws NonUniqueResultException
     */
    public function findActiveUnitByExactName($name)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nama = :name')
            ->andWhere('u.tanggalAktif < :now')
            ->andWhere('u.tanggalNonaktif is null or u.tanggalNonaktif > :now')
            ->setParameter('name', $name)
            ->setParameter('now', new DateTime('now'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}

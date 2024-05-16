<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Kantor;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Kantor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kantor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kantor[]    findAll()
 * @method Kantor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KantorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kantor::class);
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findKantorByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('k')
            ->andWhere('lower(k.nama) LIKE :val')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAllActiveKantor(): mixed
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.tanggalAktif < :now')
            ->andWhere('k.tanggalNonaktif is null or k.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAllActiveKantorData(): mixed
    {
        // Fetch parent entities with necessary fields
        $qb = $this->createQueryBuilder('k')
            ->select([
                'k.id', 'k.nama', 'k.level', 'k.tanggalAktif', 'k.tanggalNonaktif',
                'k.sk', 'k.alamat', 'k.telp', 'k.fax', 'k.zonaWaktu', 'k.latitude',
                'k.longitude', 'k.legacyKode', 'k.legacyKodeKpp', 'k.legacyKodeKanwil',
                'k.provinsi', 'k.kabupatenKota', 'k.kecamatan', 'k.kelurahan',
                'k.provinsiName', 'k.kabupatenKotaName', 'k.kecamatanName',
                'k.kelurahanName', 'k.ministryOfficeCode',
                'jenisKantor.id AS jenisKantorId', 'parent.id AS parentId', 'pembina.id AS pembinaId'
            ])
            ->leftJoin('k.jenisKantor', 'jenisKantor')
            ->leftJoin('k.parent', 'parent')
            ->leftJoin('k.pembina', 'pembina')
            ->andWhere('k.tanggalAktif < :now')
            ->andWhere('k.tanggalNonaktif IS NULL OR k.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC');

        $parentKantors = $qb->getQuery()->getArrayResult();

        // Fetch children and membina separately in batch
        $parentIds = array_map(fn($k) => (string) $k['id'], $parentKantors);  // Ensure IDs are strings

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

        // Index children and membina by parentId and pembinaId for faster lookup
        $childrenByParent = [];
        foreach ($childEntities as $child) {
            $parentId = (string) $child['parentId'];  // Ensure parentId is a string
            if (!isset($childrenByParent[$parentId])) {
                $childrenByParent[$parentId] = [];
            }
            $childrenByParent[$parentId][] = (string) $child['id'];  // Ensure child ID is a string
        }

        $membinaByPembina = [];
        foreach ($membinaEntities as $membina) {
            $pembinaId = (string) $membina['pembinaId'];  // Ensure pembinaId is a string
            if (!isset($membinaByPembina[$pembinaId])) {
                $membinaByPembina[$pembinaId] = [];
            }
            $membinaByPembina[$pembinaId][] = (string) $membina['id'];  // Ensure membina ID is a string
        }

        // Transform the results to the desired format
        $result = [];
        foreach ($parentKantors as $parent) {
            $parentId = (string) $parent['id'];  // Ensure parent ID is a string
            $result[] = [
                'id' => $parentId,
                'nama' => $parent['nama'],
                'level' => $parent['level'],
                'jenisKantor' => $parent['jenisKantorId'] ?? null,
                'parent' => $parent['parentId'] ?? null,
                'childs' => $childrenByParent[$parentId] ?? [],
                'tanggalAktif' => $parent['tanggalAktif'],
                'tanggalNonaktif' => $parent['tanggalNonaktif'],
                'sk' => $parent['sk'],
                'alamat' => $parent['alamat'],
                'telp' => $parent['telp'],
                'fax' => $parent['fax'],
                'zonaWaktu' => $parent['zonaWaktu'],
                'latitude' => $parent['latitude'],
                'longitude' => $parent['longitude'],
                'legacyKode' => $parent['legacyKode'],
                'legacyKodeKpp' => $parent['legacyKodeKpp'],
                'legacyKodeKanwil' => $parent['legacyKodeKanwil'],
                'provinsi' => $parent['provinsi'],
                'kabupatenKota' => $parent['kabupatenKota'],
                'kecamatan' => $parent['kecamatan'],
                'kelurahan' => $parent['kelurahan'],
                'provinsiName' => $parent['provinsiName'],
                'KabupatenKotaName' => $parent['kabupatenKotaName'],
                'kecamatanName' => $parent['kecamatanName'],
                'kelurahanName' => $parent['kelurahanName'],
                'ministryOfficeCode' => $parent['ministryOfficeCode'],
                'pembina' => $parent['pembinaId'] ?? null,
                'membina' => $membinaByPembina[$parentId] ?? [],
                // Include other properties you need from the parent entity
            ];
        }

        return $result;
    }



    /**
     * @param $keyword
     * @return mixed
     */
    public function findActiveKantorByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('k')
            ->andWhere('lower(k.nama) LIKE :val')
            ->andWhere('k.tanggalAktif < :now')
            ->andWhere('k.tanggalNonaktif is null or k.tanggalNonaktif > :now')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function findLegacyDataFromArrayOfIds($ids): mixed
    {
        return $this->createQueryBuilder('k')
            ->select([
                'k.id',
                'k.nama',
                'k.level',
                'k.legacyKode',
                'k.legacyKodeKpp',
                'k.legacyKodeKanwil'
            ])
            ->andWhere('k.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->addOrderBy('k.level', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findActiveKantorByExactName($name): mixed
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.nama = :name')
            ->andWhere('k.tanggalAktif < :now')
            ->andWhere('k.tanggalNonaktif is null or k.tanggalNonaktif > :now')
            ->setParameter('name', $name)
            ->setParameter('now', new DateTime('now'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}

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
        $kantorAll = $this->createQueryBuilder('kantor')
            ->select([
                'kantor.id', 'kantor.nama', 'kantor.level', 'kantor.tanggalAktif', 'kantor.tanggalNonaktif',
                'kantor.sk', 'kantor.alamat', 'kantor.telp', 'kantor.fax', 'kantor.zonaWaktu', 'kantor.latitude',
                'kantor.longitude', 'kantor.legacyKode', 'kantor.legacyKodeKpp', 'kantor.legacyKodeKanwil',
                'kantor.provinsi', 'kantor.kabupatenKota', 'kantor.kecamatan', 'kantor.kelurahan',
                'kantor.provinsiName', 'kantor.kabupatenKotaName', 'kantor.kecamatanName',
                'kantor.kelurahanName', 'kantor.ministryOfficeCode',
                'jenisKantor.id AS jenisKantorId', 'parent.id AS parentId', 'pembina.id AS pembinaId'
            ])
            ->leftJoin('kantor.jenisKantor', 'jenisKantor')
            ->leftJoin('kantor.parent', 'parent')
            ->leftJoin('kantor.pembina', 'pembina')
            ->andWhere('kantor.tanggalAktif < :now')
            ->andWhere('kantor.tanggalNonaktif IS NULL OR kantor.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('kantor.level', 'ASC')
            ->addOrderBy('kantor.nama', 'ASC');

        $parentKantors = $kantorAll->getQuery()->getArrayResult();

        $parentIds = array_map(fn($kantor) => (string) $kantor['id'], $parentKantors);

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
            $childrenByParent[$parentId][] = (string) ('/api/kantors/' . $child['id']);
        }

        $membinaByPembina = [];
        foreach ($membinaEntities as $membina) {
            $pembinaId = (string) $membina['pembinaId'];
            if (!isset($membinaByPembina[$pembinaId])) {
                $membinaByPembina[$pembinaId] = [];
            }
            $membinaByPembina[$pembinaId][] = (string) ('/api/kantors/' . $membina['id']);
        }

        $result = [];
        foreach ($parentKantors as $parent) {
            $parentId = (string) $parent['id'];  // Ensure parent ID is a string
            $result[] = [
                'id' => $parentId,
                'nama' => $parent['nama'],
                'level' => $parent['level'],
                'jenisKantor' => !empty($parent['jenisKantorId']) ? ('/api/jenis_kantors/' . $parent['jenisKantorId']) : null,
                'parent' => !empty($parent['parentId']) ? ('/api/kantors/' . $parent['parentId']) : null,
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
                'pembina' => !empty($parent['pembinaId'])? ('/api/kantors/' . $parent['pembinaId']) : null,
                'membina' => $membinaByPembina[$parentId] ?? [],
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

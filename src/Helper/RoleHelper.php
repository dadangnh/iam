<?php


namespace App\Helper;


use App\Entity\Aplikasi\Aplikasi;
use App\Entity\Core\Permission;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\JabatanPegawaiLuar;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectManager;

class RoleHelper
{
    /**
     * @param JabatanPegawai $jabatanPegawai
     * @param ObjectManager $objectManager
     * @return array
     * @throws Exception
     */
    public static function getRolesFromJabatanPegawai(ObjectManager  $objectManager,
                                                      JabatanPegawai $jabatanPegawai): array
    {
        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor, 11 => jabatan + unit + jenis kantor, 12 => jabatan + jenis kantor, 13 => eselon + jenis kantor"
        $roles = [];
        $plainRoles = [];
        $jabatan = $jabatanPegawai->getJabatan();
        $unit = $jabatanPegawai->getUnit();
        $kantor = $jabatanPegawai->getKantor();
        $jenisKantorKantor = $kantor?->getJenisKantor();
        $jenisKantorUnit = $unit?->getJenisKantor();
        $pegawai = $jabatanPegawai->getPegawai();
        $pegawaiId = $pegawai->getId();
        $eselon = $jabatan->getEselon();

        if ($jenisKantorKantor === $jenisKantorUnit
            && null !== $jenisKantorKantor
            && null !== $jenisKantorUnit
        ) {
            $jenisKantor = $jenisKantorKantor;
        } else {
            $jenisKantor = null;
        }

        // check from jabatan
        if (null !== $jabatan) {
            // direct role from jabatan/ jabatan unit/ jabatan kantor/ combination
            foreach ($jabatan->getRoles() as $role) {
                if (true === $role->isOperator()) {
                    $roleCombination = $objectManager
                        ->getRepository(JabatanPegawai::class)
                        ->findRoleCombinationByPegawai($pegawaiId);
                    if (2 === $role->getJenis() && $role->getNama()) {
                        if (null !== $roleCombination['role']) {
                            $arrRole =  explode(',', $roleCombination['role']);
                            foreach ( $arrRole as $key => $val ){
                                $plainRoles[] = $val;
                            }
                        }
                    }
                } else {
                    if (2 === $role->getJenis()) {
                        $roles[] = $role;
                    } elseif (8 === $role->getJenis() && $role->getUnits()->contains($unit)) {
                        $roles[] = $role;
                    } elseif (9 === $role->getJenis() && $role->getKantors()->contains($kantor)) {
                        $roles[] = $role;
                    } elseif (10 === $role->getJenis()
                        && $role->getUnits()->contains($unit)
                        && $role->getKantors()->contains($kantor)
                    ) {
                        $roles[] = $role;
                    } elseif (11 === $role->getJenis()
                        && $role->getUnits()->contains($unit)
                        && $role->getJenisKantors()->contains($jenisKantor)
                    ) {
                        $roles[] = $role;
                    } elseif (12 === $role->getJenis()
                        && $role->getJenisKantors()->contains($jenisKantor)
                    ) {
                        $roles[] = $role;
                    }
                }
            }

            // get eselon level
            if (null !== $eselon) {
                foreach ($eselon->getRoles() as $role) {
                    if (5 === $role->getJenis()) {
                        $roles[] = $role;
                    } elseif (13 === $role->getJenis()
                        && $role->getJenisKantors()->contains($jenisKantor)
                    ) {
                        $roles[] = $role;
                    }
                }
            }
        }

        // get role from unit
        if (null !== $unit) {
            foreach ($unit->getRoles() as $role) {
                if (3 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from kantor
        if (null !== $kantor) {
            foreach ($kantor->getRoles() as $role) {
                if (4 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from eselon
        if (null !== $jabatan->getEselon()) {
            foreach ($jabatan->getEselon()->getRoles() as $role) {
                if (5 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from jenis kantor
        if (null !== $jenisKantor) {
            foreach ($jenisKantor->getRoles() as $role) {
                if (6 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        /** @var Role $role */
        foreach ($roles as $role) {
            if ($role->getStartDate() <= new DateTimeImmutable('now')
                && ($role->getEndDate() >= new DateTimeImmutable('now')
                    || null === $role->getEndDate())
            ) {
                $plainRoles[] = $role->getNama();
            }
        }

        return array_values(array_unique($plainRoles));
    }

    /**
     * @param JabatanPegawaiLuar $jabatanPegawaiLuar
     * @param ObjectManager $objectManager
     * @return array
     * @throws Exception
     */
    public static function getRolesFromJabatanPegawaiLuar(ObjectManager  $objectManager,
                                                      JabatanPegawaiLuar $jabatanPegawaiLuar): array
    {
        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor, 11 => jabatan + unit + jenis kantor, 12 => jabatan + jenis kantor, 13 => eselon + jenis kantor,
        // 14 => jabatan luar, 15 => jabatan luar + unit luar, 16 => jabatan luar + kantor luar, 17 => jabatan luar + unit luar + kantor luar,
        // 18 => unit luar, 19 => kantor luar"
        $roles = [];
        $plainRoles = [];
        $jabatanLuar = $jabatanPegawaiLuar->getJabatanLuar();
        $unitLuar = $jabatanPegawaiLuar->getUnitLuar();
        $kantorLuar = $jabatanPegawaiLuar->getKantorLuar();
        $jenisKantorLuar = $kantorLuar?->getJenisKantorLuar();
        $jenisKantorUnit = $unitLuar?->getJenisKantorLuar();
        $pegawaiLuar = $jabatanPegawaiLuar->getPegawaiLuar();
        $pegawaiIdLuar = $pegawaiLuar->getId();
        $eselon = $jabatanLuar->getEselon();

        if ($jenisKantorLuar === $jenisKantorUnit
            && null !== $jenisKantorLuar
            && null !== $jenisKantorUnit
        ) {
            $jenisKantor = $jenisKantorLuar;
        } else {
            $jenisKantor = null;
        }

        // check from jabatan
        if (null !== $jabatanLuar) {
            // direct role from jabatan/ jabatan unit/ jabatan kantor/ combination
            foreach ($jabatanLuar->getRoles() as $role) {
                if (14 === $role->getJenis()) {
                    $roles[] = $role;
                } elseif (15 === $role->getJenis() && $role->getUnitLuars()->contains($unitLuar)) {
                    $roles[] = $role;
                } elseif (16 === $role->getJenis() && $role->getKantorLuars()->contains($kantorLuar)) {
                    $roles[] = $role;
                } elseif (17 === $role->getJenis()
                    && $role->getUnitLuars()->contains($unitLuar)
                    && $role->getKantorLuars()->contains($kantorLuar)
                ) {
                    $roles[] = $role;
                }
            }
        }
        // get role from unit
        if (null !== $unitLuar) {
            foreach ($unitLuar->getRoles() as $role) {
                if (18 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from kantor
        if (null !== $kantorLuar) {
            foreach ($kantorLuar->getRoles() as $role) {
                if (19 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }
        /** @var Role $role */
        foreach ($roles as $role) {
            if ($role->getStartDate() <= new DateTimeImmutable('now')
                && ($role->getEndDate() >= new DateTimeImmutable('now')
                    || null === $role->getEndDate())
            ) {
                $plainRoles[] = $role->getNama();
            }
        }

        return array_values(array_unique($plainRoles));
    }

    /**
     * @param JabatanPegawai $jabatanPegawai
     * @param ObjectManager $objectManager
     * @return array
     * @throws Exception
     */
    public static function getPlainRolesNameFromJabatanPegawai(ObjectManager  $objectManager,
                                                               JabatanPegawai $jabatanPegawai): array
    {
        return self::getRolesFromJabatanPegawai($objectManager, $jabatanPegawai);
    }

    /**
     * @param JabatanPegawaiLuar $jabatanPegawaiLuar
     * @param ObjectManager $objectManager
     * @return array
     * @throws Exception
     */
    public static function getPlainRolesNameFromJabatanPegawaiLuar(ObjectManager  $objectManager,
                                                               JabatanPegawaiLuar $jabatanPegawaiLuar): array
    {
        return self::getRolesFromJabatanPegawaiLuar($objectManager, $jabatanPegawaiLuar);
    }


    /**
     * @param Role $role
     * @return array
     */
    public static function getAplikasiByRole(Role $role): array
    {
        $permissions = $role?->getPermissions();
        $listAplikasi = [];
        if (null !== $permissions) {
            /** @var Permission $permission */
            foreach ($permissions as $permission) {
                $moduls = $permission?->getModul();
                if (null !== $moduls) {
                    foreach ($moduls as $modul) {
                        if ($modul->getStatus()) {
                            /** @var Aplikasi $aplikasi */
                            $aplikasi = $modul->getAplikasi();
                            if ($aplikasi->getStatus() && !in_array($aplikasi, $listAplikasi, true)) {
                                $listAplikasi[] = $aplikasi;
                            }
                        }
                    }
                }
            }
        }

        return $listAplikasi;
    }

    /**
     * @param Role $role
     * @return array
     */
    public static function getAplikasiByRole1(Role $role): array
    {
        $aplikasis = $role?->getAplikasis();
        $listAplikasi = [];
        if (null !== $aplikasis) {
            foreach ($aplikasis as $aplikasi) {
                $listAplikasi[] = $aplikasi->getNama();
            }
        }

        return $listAplikasi;
    }

    /**
     * @param array $roles
     * @return array
     */
    public static function getAplikasiByArrayOfRoles(array $roles): array
    {
        $listAplikasi = [];
        /** @var Role $role */
        foreach ($roles as $role) {
            $listAplikasi[] = self::getAplikasiByRole($role);
        }

        return array_merge(...$listAplikasi);
    }

    /**
     * @param Role $role
     * @return array
     */
    public static function getAllAplikasiByRole(Role $role): array
    {
        $permissions = $role?->getPermissions();
        $listAplikasi = [];
        if (null !== $permissions) {
            /** @var Permission $permission */
            foreach ($permissions as $permission) {
                $moduls = $permission?->getModul();
                if (null !== $moduls) {
                    foreach ($moduls as $modul) {
                        $aplikasi = $modul->getAplikasi();
                        if (!in_array($aplikasi, $listAplikasi, true)) {
                            $listAplikasi[] = $aplikasi;
                        }
                    }
                }
            }
        }

        return $listAplikasi;
    }

    /**
     * @param array $roles
     * @return array
     */
    public static function getAllAplikasiByArrayOfRoles(array $roles): array
    {
        $listAplikasi = [];
        /** @var Role $role */
        foreach ($roles as $role) {
            $listAplikasi[] = self::getAllAplikasiByRole($role);
        }

        return array_merge(...$listAplikasi);
    }

    /**
     * @param JabatanPegawai $jabatanPegawai
     * @param ObjectManager $objectManager
     * @return array
     * @throws Exception
     */
    public static function getAplikasiFromJabatanPegawai(ObjectManager  $objectManager,
                                                      JabatanPegawai $jabatanPegawai): array
    {
        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor, 11 => jabatan + unit + jenis kantor, 12 => jabatan + jenis kantor, 13 => eselon + jenis kantor"
        $roles = [];
        $plainRoles = [];
        $listAplikasi = [];
        $jabatan = $jabatanPegawai->getJabatan();
        $unit = $jabatanPegawai->getUnit();
        $kantor = $jabatanPegawai->getKantor();
        $jenisKantorKantor = $kantor?->getJenisKantor();
        $jenisKantorUnit = $unit?->getJenisKantor();
        $pegawai = $jabatanPegawai->getPegawai();
        $pegawaiId = $pegawai->getId();
        $eselon = $jabatan->getEselon();

        if ($jenisKantorKantor === $jenisKantorUnit
            && null !== $jenisKantorKantor
            && null !== $jenisKantorUnit
        ) {
            $jenisKantor = $jenisKantorKantor;
        } else {
            $jenisKantor = null;
        }

        // check from jabatan
        if (null !== $jabatan) {
            // direct role from jabatan/ jabatan unit/ jabatan kantor/ combination
            foreach ($jabatan->getRoles() as $role) {
                if (true === $role->isOperator()) {
                    $roleCombination = $objectManager
                        ->getRepository(JabatanPegawai::class)
                        ->findRoleCombinationByPegawai($pegawaiId);
                    if (2 === $role->getJenis() && $role->getNama()) {
                        if (null !== $roleCombination['role']) {
                            $arrRole =  explode(',', $roleCombination['role']);
                            foreach ( $arrRole as $key => $val ){
                                $plainRoles[] = $val;
                            }
                        }
                    }
                } else {
                    if (2 === $role->getJenis()) {
                        $roles[] = $role;
                    } elseif (8 === $role->getJenis() && $role->getUnits()->contains($unit)) {
                        $roles[] = $role;
                    } elseif (9 === $role->getJenis() && $role->getKantors()->contains($kantor)) {
                        $roles[] = $role;
                    } elseif (10 === $role->getJenis()
                        && $role->getUnits()->contains($unit)
                        && $role->getKantors()->contains($kantor)
                    ) {
                        $roles[] = $role;
                    } elseif (11 === $role->getJenis()
                        && $role->getUnits()->contains($unit)
                        && $role->getJenisKantors()->contains($jenisKantor)
                    ) {
                        $roles[] = $role;
                    } elseif (12 === $role->getJenis()
                        && $role->getJenisKantors()->contains($jenisKantor)
                    ) {
                        $roles[] = $role;
                    }
                }
            }

            // get eselon level
            if (null !== $eselon) {
                foreach ($eselon->getRoles() as $role) {
                    if (5 === $role->getJenis()) {
                        $roles[] = $role;
                    } elseif (13 === $role->getJenis()
                        && $role->getJenisKantors()->contains($jenisKantor)
                    ) {
                        $roles[] = $role;
                    }
                }
            }
        }

        // get role from unit
        if (null !== $unit) {
            foreach ($unit->getRoles() as $role) {
                if (3 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from kantor
        if (null !== $kantor) {
            foreach ($kantor->getRoles() as $role) {
                if (4 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from eselon
        if (null !== $jabatan->getEselon()) {
            foreach ($jabatan->getEselon()->getRoles() as $role) {
                if (5 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from jenis kantor
        if (null !== $jenisKantor) {
            foreach ($jenisKantor->getRoles() as $role) {
                if (6 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        /** @var Role $role */
        foreach ($roles as $role) {
            if ($role->getStartDate() <= new DateTimeImmutable('now')
                && ($role->getEndDate() >= new DateTimeImmutable('now')
                    || null === $role->getEndDate())
            ) {
                $aplikasis = $role?->getAplikasis();
                if (null !== $aplikasis) {
                    foreach ($aplikasis as $aplikasi) {
                        $listAplikasi[] = $aplikasi->getNama();
                    }
                }
            }
        }

        return array_values(array_unique($listAplikasi));
    }

    /**
     * @param JabatanPegawaiLuar $jabatanPegawaiLuar
     * @param ObjectManager $objectManager
     * @return array
     * @throws Exception
     */
    public static function getAplikasiFromJabatanPegawaiLuar(ObjectManager  $objectManager,
                                                          JabatanPegawaiLuar $jabatanPegawaiLuar): array
    {
        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor, 11 => jabatan + unit + jenis kantor, 12 => jabatan + jenis kantor, 13 => eselon + jenis kantor,
        // 14 => jabatan luar, 15 => jabatan luar + unit luar, 16 => jabatan luar + kantor luar, 17 => jabatan luar + unit luar + kantor luar,
        // 18 => unit luar, 19 => kantor luar"
        $roles = [];
        $plainRoles = [];
        $listAplikasi = [];
        $jabatanLuar = $jabatanPegawaiLuar->getJabatanLuar();
        $unitLuar = $jabatanPegawaiLuar->getUnitLuar();
        $kantorLuar = $jabatanPegawaiLuar->getKantorLuar();
        $jenisKantorLuar = $kantorLuar?->getJenisKantorLuar();
        $jenisKantorUnit = $unitLuar?->getJenisKantorLuar();
        $pegawaiLuar = $jabatanPegawaiLuar->getPegawaiLuar();
        $pegawaiIdLuar = $pegawaiLuar->getId();
        $eselon = $jabatanLuar->getEselon();

        if ($jenisKantorLuar === $jenisKantorUnit
            && null !== $jenisKantorLuar
            && null !== $jenisKantorUnit
        ) {
            $jenisKantor = $jenisKantorLuar;
        } else {
            $jenisKantor = null;
        }

        // check from jabatan
        if (null !== $jabatanLuar) {
            // direct role from jabatan/ jabatan unit/ jabatan kantor/ combination
            foreach ($jabatanLuar->getRoles() as $role) {
                if (14 === $role->getJenis()) {
                    $roles[] = $role;
                } elseif (15 === $role->getJenis() && $role->getUnitLuars()->contains($unitLuar)) {
                    $roles[] = $role;
                } elseif (16 === $role->getJenis() && $role->getKantorLuars()->contains($kantorLuar)) {
                    $roles[] = $role;
                } elseif (17 === $role->getJenis()
                    && $role->getUnitLuars()->contains($unitLuar)
                    && $role->getKantorLuars()->contains($kantorLuar)
                ) {
                    $roles[] = $role;
                }
            }
        }
        // get role from unit
        if (null !== $unitLuar) {
            foreach ($unitLuar->getRoles() as $role) {
                if (18 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }

        // get role from kantor
        if (null !== $kantorLuar) {
            foreach ($kantorLuar->getRoles() as $role) {
                if (19 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }
        }
        /** @var Role $role */
        foreach ($roles as $role) {
            if ($role->getStartDate() <= new DateTimeImmutable('now')
                && ($role->getEndDate() >= new DateTimeImmutable('now')
                    || null === $role->getEndDate())
            ) {
                $aplikasis = $role?->getAplikasis();
                if (null !== $aplikasis) {
                    foreach ($aplikasis as $aplikasi) {
                        $listAplikasi[] = $aplikasi->getNama();
                    }
                }
            }
        }

        return array_values(array_unique($listAplikasi));
    }
}

<?php


namespace App\utils;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use JetBrains\PhpStorm\ArrayShape;

class RoleUtils
{

    /**
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    public static function getRolesFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor"
        $roles = [];
        $jabatan = $jabatanPegawai->getJabatan();
        $unit = $jabatanPegawai->getUnit();
        $kantor = $jabatanPegawai->getKantor();

        // check from jabatan
        if (null !== $jabatan) {
            // direct role from jabatan/ jabatan unit/ jabatan kantor/ combination
            foreach ($jabatan->getRoles() as $role) {
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
                }
            }

            // get eselon level
            $eselon = $jabatan->getEselon();
            if (null !== $eselon) {
                foreach ($eselon->getRoles() as $role) {
                    if (5 === $role->getJenis()) {
                        $roles[] = $role;
                    }
                }
            }

            // get role from unit
        } elseif (null !== $unit) {
            foreach ($unit->getRoles() as $role) {
                if (3 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }

            // get jenis kantor
            $jenisKantor = $unit->getJenisKantor();
            if (null !== $jenisKantor) {
                foreach ($jenisKantor->getRoles() as $role) {
                    if (6 === $role->getJenis()) {
                        $roles[] = $role;
                    }
                }
            }

            // get role from kantor
        } elseif (null !== $kantor) {
            foreach ($kantor->getRoles() as $role) {
                if (4 === $role->getJenis()) {
                    $roles[] = $role;
                }
            }

            // get jenis kantor
            $jenisKantor = $kantor->getJenisKantor();
            if (null !== $jenisKantor) {
                foreach ($jenisKantor->getRoles() as $role) {
                    if (6 === $role->getJenis()) {
                        $roles[] = $role;
                    }
                }
            }
        }
        return $roles;
    }

    public static function getPlainRolesNameFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        $roles = self::getRolesFromJabatanPegawai($jabatanPegawai);
        $plainRoles = [];

        /** @var Role $role */
        foreach ($roles as $role) {
            $plainRoles[] = $role->getNama();
        }
        return $plainRoles;
    }

    #[ArrayShape([
        'iri' => "string",
        'id' => "null|string",
        'nama' => "null|string",
        'deskripsi' => "null|string",
        'level' => "int|null"
    ])]
    public static function createRoleDefaultResponseFromRole(Role $role, IriConverterInterface $iriConverter): array
    {
        return [
            'iri' => $iriConverter->getIriFromItem($role),
            'id' => $role->getId(),
            'nama' => $role->getNama(),
            'deskripsi' => $role->getDeskripsi(),
            'level' => $role->getLevel()
        ];
    }

    public static function createRoleDefaultResponseFromArrayOfRoles(array $roles, IriConverterInterface $iriConverter): array
    {
        $response = [];

        /** @var Role $role */
        foreach ($roles as $role) {
            $response[] = self::createRoleDefaultResponseFromRole($role, $iriConverter);
        }

        return $response;
    }
}

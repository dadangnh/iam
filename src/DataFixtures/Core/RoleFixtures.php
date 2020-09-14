<?php

namespace App\DataFixtures\Core;

use App\Entity\Core\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const ROLE_SUPER_ADMIN = 'super-admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_UPK_PUSAT = 'upk-pusat';

    public function load(ObjectManager $manager)
    {
        $superAdminRole = new Role();
        $superAdminRole->setNama('ROLE_SUPER_ADMIN');
        $superAdminRole->setSystemName('ROLE_SUPER_ADMIN');
        $superAdminRole->setDeskripsi('Default Super Admin Role');
        $superAdminRole->setJenis(1);
        $superAdminRole->setLevel(0);
        $manager->persist($superAdminRole);

        $adminRole = new Role();
        $adminRole->setNama('ROLE_ADMIN');
        $adminRole->setSystemName('ROLE_ADMIN');
        $adminRole->setDeskripsi('Default Admin Role');
        $adminRole->setJenis(1);
        $adminRole->setLevel(1);
        $adminRole->setSubsOfRole($superAdminRole);
        $manager->persist($adminRole);

        $userRole = new Role();
        $userRole->setNama('ROLE_USER');
        $userRole->setSystemName('ROLE_USER');
        $userRole->setDeskripsi('Default User Role');
        $userRole->setJenis(1);
        $userRole->setLevel(0);
        $manager->persist($userRole);

        $upkPusatRole = new Role();
        $upkPusatRole->setNama('ROLE_UPK_PUSAT');
        $upkPusatRole->setSystemName('ROLE_UPK_PUSAT');
        $upkPusatRole->setDeskripsi('Default UPK Pusat Role');
        $upkPusatRole->setJenis(10);
        $upkPusatRole->setLevel(0);
        $manager->persist($upkPusatRole);

        $upkWilayahRole = new Role();
        $upkWilayahRole->setNama('ROLE_UPK_WILAYAH');
        $upkWilayahRole->setSystemName('ROLE_UPK_WILAYAH');
        $upkWilayahRole->setDeskripsi('Default UPK Wilayah Role');
        $upkWilayahRole->setJenis(10);
        $upkWilayahRole->setLevel(1);
        $upkWilayahRole->setSubsOfRole($upkPusatRole);
        $manager->persist($upkWilayahRole);

        $upkLokalRole = new Role();
        $upkLokalRole->setNama('ROLE_UPK_LOKAL');
        $upkLokalRole->setSystemName('ROLE_UPK_LOKAL');
        $upkLokalRole->setDeskripsi('Default UPK Lokal Role');
        $upkLokalRole->setJenis(10);
        $upkLokalRole->setLevel(0);
        $manager->persist($upkLokalRole);

        $manager->flush();

        $this->addReference(self::ROLE_SUPER_ADMIN, $superAdminRole);
        $this->addReference(self::ROLE_ADMIN, $adminRole);
        $this->addReference(self::ROLE_UPK_PUSAT, $upkPusatRole);
    }
}

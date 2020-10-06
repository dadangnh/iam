<?php

namespace App\DataFixtures\Pegawai;

use App\DataFixtures\Organisasi\OrganisasiFixtures;
use App\Entity\Pegawai\JabatanPegawai;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JabatanPegawaiFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        Matikan dulu karena fail load
//        $jabatan1 = new JabatanPegawai();
//        $jabatan1->setPegawai(PegawaiFixtures::PEGAWAI_1);
//        $jabatan1->setJabatan(OrganisasiFixtures::JABATAN_MENTERI);
//        $jabatan1->setTipe(OrganisasiFixtures::TIPE_DEFINITIF);
//        $jabatan1->setKantor(OrganisasiFixtures::KANTOR_KEMENTERIAN);
//        $jabatan1->setUnit(OrganisasiFixtures::UNIT_KEMENTERIAN);
//        $manager->persist($jabatan1);
//
//        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PegawaiFixtures::class,
            OrganisasiFixtures::class
        ];
    }
}

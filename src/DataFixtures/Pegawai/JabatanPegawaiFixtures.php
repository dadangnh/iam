<?php

namespace App\DataFixtures\Pegawai;

use App\DataFixtures\Organisasi\OrganisasiFixtures;
use App\Entity\Pegawai\JabatanPegawai;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JabatanPegawaiFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jabatan1 = new JabatanPegawai();
        $jabatan1->setPegawai($this->getReference(PegawaiFixtures::PEGAWAI_1));
        $jabatan1->setJabatan($this->getReference(OrganisasiFixtures::JABATAN_MENTERI));
        $jabatan1->setTipe($this->getReference(OrganisasiFixtures::TIPE_DEFINITIF));
        $jabatan1->setKantor($this->getReference(OrganisasiFixtures::KANTOR_KEMENTERIAN));
        $jabatan1->setUnit($this->getReference(OrganisasiFixtures::UNIT_KEMENTERIAN));
        $manager->persist($jabatan1);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PegawaiFixtures::class,
            OrganisasiFixtures::class
        ];
    }
}

<?php

namespace App\DataFixtures\Pegawai;

use App\DataFixtures\Core\NewUserFixtures;
use App\Entity\Pegawai\Pegawai;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PegawaiFixtures extends Fixture implements DependentFixtureInterface
{
    public const PEGAWAI_1 = 'pegawai-1';

    public function load(ObjectManager $manager)
    {
//        Matikan dulu karena fail load
//        $pegawai1 = new Pegawai();
//        $pegawai1->setNama('Pegawai 1');
//        $pegawai1->setUser(NewUserFixtures::USER_SUPER_ADMIN);
//        $pegawai1->setTempatLahir('Jakarta');
//        $pegawai1->setTanggalLahir(new DateTimeImmutable());
//        $pegawai1->setJenisKelamin(JenisKelaminFixtures::LAKI);
//        $pegawai1->setAgama(AgamaFixtures::AGAMA_1);
//        $pegawai1->setNik('9999999999999999');
//        $pegawai1->setNip9('999999999');
//        $pegawai1->setNip18('999999999999999999');
//        $manager->persist($pegawai1);
//
//        $manager->flush();
//
//        $this->addReference(self::PEGAWAI_1, $pegawai1);
    }

    public function getDependencies()
    {
        return [
            NewUserFixtures::class,
            AgamaFixtures::class,
            JenisKelaminFixtures::class
        ];
    }
}

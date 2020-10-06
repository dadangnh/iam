<?php

namespace App\DataFixtures\Aplikasi;

use App\Entity\Aplikasi\Modul;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ModulFixtures extends Fixture implements DependentFixtureInterface
{
    public const MODUL_SATU = 'modul-1';
    public const MODUL_DUA = 'modul-2';
    public const MODUL_TIGA = 'modul-3';

    public function load(ObjectManager $manager)
    {
        $modul1 = new Modul();
        $modul1->setNama('Modul 1');
        $modul1->setSystemName('modul_1');
        $modul1->setDeskripsi('Modul 1 Aplikasi 1');
        $modul1->setStatus(true);
        $modul1->setAplikasi($this->getReference(AplikasiFixtures::APLIKASI_SATU));
        $manager->persist($modul1);

        $modul2 = new Modul();
        $modul2->setNama('Modul 2');
        $modul2->setSystemName('modul_2');
        $modul2->setDeskripsi('Modul 2 Aplikasi 1');
        $modul2->setStatus(false);
        $modul2->setAplikasi($this->getReference(AplikasiFixtures::APLIKASI_SATU));
        $manager->persist($modul2);

        $modul3 = new Modul();
        $modul3->setNama('Modul 3');
        $modul3->setSystemName('modul_3');
        $modul3->setDeskripsi('Modul dari Aplikasi 2');
        $modul3->setStatus(false);
        $modul3->setAplikasi($this->getReference(AplikasiFixtures::APLIKASI_DUA));
        $manager->persist($modul3);

        $manager->flush();

        $this->addReference(self::MODUL_SATU, $modul1);
        $this->addReference(self::MODUL_DUA, $modul2);
        $this->addReference(self::MODUL_TIGA, $modul3);
    }

    public function getDependencies()
    {
        return [
            AplikasiFixtures::class
        ];
    }
}

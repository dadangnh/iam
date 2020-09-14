<?php

namespace App\DataFixtures\Aplikasi;

use App\Entity\Aplikasi\Aplikasi;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AplikasiFixtures extends Fixture
{
    public const APLIKASI_SATU = 'app1';
    public const APLIKASI_DUA = 'app2';

    public function load(ObjectManager $manager)
    {
        $aplikasi1 = new Aplikasi();
        $aplikasi1->setNama('Aplikasi 1');
        $aplikasi1->setSystemName('aplikasi_1');
        $aplikasi1->setDeskripsi('Test Aplikasi 1');
        $aplikasi1->setStatus(true);
        $manager->persist($aplikasi1);

        $aplikasi2 = new Aplikasi();
        $aplikasi2->setNama('Aplikasi 2');
        $aplikasi2->setSystemName('aplikasi_2');
        $aplikasi2->setDeskripsi('Test Aplikasi 2');
        $aplikasi2->setStatus(false);
        $manager->persist($aplikasi2);

        $manager->flush();
        $this->addReference(self::APLIKASI_SATU, $aplikasi1);
        $this->addReference(self::APLIKASI_DUA, $aplikasi2);
    }
}

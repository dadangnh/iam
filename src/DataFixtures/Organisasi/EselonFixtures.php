<?php

namespace App\DataFixtures\Organisasi;

use App\Entity\Organisasi\Eselon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EselonFixtures extends Fixture
{
    public const MENTERI = 'menteri';
    public const ESELON_SATU = 'eselon1';
    public const ESELON_DUA = 'eselon2';
    public const ESELON_TIGA = 'eselon3';
    public const ESELON_EMPAT = 'eselon4';

    public function load(ObjectManager $manager)
    {
        $menteri = new Eselon();
        $menteri->setNama('Menteri');
        $menteri->setTingkat(0);
        $menteri->setKode('');
        $menteri->setLegacyKode(0);
        $manager->persist($menteri);

        $eselon1 = new Eselon();
        $eselon1->setNama('Eselon 1');
        $eselon1->setTingkat(1);
        $eselon1->setKode('I');
        $eselon1->setLegacyKode(10);
        $manager->persist($eselon1);

        $eselon2 = new Eselon();
        $eselon2->setNama('Eselon 2');
        $eselon2->setTingkat(2);
        $eselon2->setKode('II');
        $eselon2->setLegacyKode(20);
        $manager->persist($eselon2);

        $eselon3 = new Eselon();
        $eselon3->setNama('Eselon 3');
        $eselon3->setTingkat(3);
        $eselon3->setKode('III');
        $eselon3->setLegacyKode(30);
        $manager->persist($eselon3);

        $eselon4 = new Eselon();
        $eselon4->setNama('Eselon 4');
        $eselon4->setTingkat(4);
        $eselon4->setKode('IV');
        $eselon4->setLegacyKode(40);
        $manager->persist($eselon4);

        $manager->flush();

        $this->addReference(self::MENTERI, $menteri);
        $this->addReference(self::ESELON_SATU, $eselon1);
        $this->addReference(self::ESELON_DUA, $eselon2);
        $this->addReference(self::ESELON_TIGA, $eselon3);
        $this->addReference(self::ESELON_EMPAT, $eselon4);
    }
}

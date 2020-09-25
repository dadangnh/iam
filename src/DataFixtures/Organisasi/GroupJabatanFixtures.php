<?php

namespace App\DataFixtures\Organisasi;

use App\Entity\Organisasi\GroupJabatan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupJabatanFixtures extends Fixture
{
    public const FPP = 'fpp';
    public const PENILAI = 'penilai';
    public const WIDYAISWARA = 'widyaiswara';
    public const PRAKOM = 'prakom';

    public function load(ObjectManager $manager)
    {
        $fpp = new GroupJabatan();
        $fpp->setNama('Fungsional Pemeriksa Pajak');
        $fpp->setLegacyKode('01');
        $manager->persist($fpp);

        $penilai = new GroupJabatan();
        $penilai->setNama('Fungsional Penilai Pajak');
        $penilai->setLegacyKode('02');
        $manager->persist($penilai);

        $widya = new GroupJabatan();
        $widya->setNama('Widyaiswara Pajak');
        $widya->setLegacyKode('03');
        $manager->persist($widya);

        $prakom = new GroupJabatan();
        $prakom->setNama('Fungsional Pranata Komputer');
        $prakom->setLegacyKode('01');
        $manager->persist($prakom);

        $manager->flush();

        $this->addReference(self::FPP, $fpp);
        $this->addReference(self::PENILAI, $penilai);
        $this->addReference(self::WIDYAISWARA, $widya);
        $this->addReference(self::PRAKOM, $prakom);
    }
}

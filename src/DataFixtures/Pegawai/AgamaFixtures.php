<?php

namespace App\DataFixtures\Pegawai;

use App\Entity\Pegawai\Agama;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgamaFixtures extends Fixture
{
    public const AGAMA_1 = 'agama-1';

    public function load(ObjectManager $manager)
    {
        $agama1 = new Agama();
        $agama1->setNama('Agama 1');
        $agama1->setLegacyKode(1);
        $manager->persist($agama1);

        $manager->flush();

        $this->addReference(self::AGAMA_1, $agama1);
    }
}

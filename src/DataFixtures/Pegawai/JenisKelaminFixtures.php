<?php

namespace App\DataFixtures\Pegawai;

use App\Entity\Pegawai\JenisKelamin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JenisKelaminFixtures extends Fixture
{
    public const LAKI = 'laki';
    public const PEREMPUAN = 'perempuan';

    public function load(ObjectManager $manager)
    {
        $laki = new JenisKelamin();
        $laki->setNama('LAKI-LAKI');
        $laki->setLegacyKode(1);
        $manager->persist($laki);

        $perempuan = new JenisKelamin();
        $perempuan->setNama('PEREMPUAN');
        $perempuan->setLegacyKode(2);
        $manager->persist($perempuan);

        $manager->flush();

        $this->addReference(self::LAKI, $laki);
        $this->addReference(self::PEREMPUAN, $perempuan);
    }
}

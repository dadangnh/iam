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

    public function load(ObjectManager $manager): void
    {
        $pegawai1 = new Pegawai();
        $pegawai1->setNama('Pegawai 1');
        $pegawai1->setUser($this->getReference(NewUserFixtures::USER_ADMIN));
        $pegawai1->setTempatLahir('Jakarta');
        $pegawai1->setTanggalLahir(new DateTimeImmutable());
        $pegawai1->setNik('9999999999999999');
        $pegawai1->setNip9('999999999');
        $pegawai1->setNip18('999999999999999999');
        $pegawai1->setOnLeave(false);
        $manager->persist($pegawai1);

        $manager->flush();

        $this->addReference(self::PEGAWAI_1, $pegawai1);
    }

    public function getDependencies(): array
    {
        return [
            NewUserFixtures::class
        ];
    }
}

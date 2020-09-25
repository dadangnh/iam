<?php

namespace App\DataFixtures\Organisasi;

use App\Entity\Organisasi\JenisKantor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JenisKantorFixtures extends Fixture
{
    public const KEMENTERIAN = 'kementerian';
    public const DJP = 'djp';
    public const SES = 'ses';

    public function load(ObjectManager $manager)
    {
        $kementerian = new JenisKantor();
        $kementerian->setNama('Kementerian');
        $kementerian->setTipe('KEMENTERIAN');
        $kementerian->setKlasifikasi(1);
        $kementerian->setLegacyId(0);
        $kementerian->setLegacyKode(0);
        $manager->persist($kementerian);

        $djp = new JenisKantor();
        $djp->setNama('Direktorat Jenderal Pajak');
        $djp->setTipe('KPDJP');
        $djp->setKlasifikasi(1);
        $djp->setLegacyId(1);
        $djp->setLegacyKode(1);
        $manager->persist($djp);

        $ses = new JenisKantor();
        $ses->setNama('Sekretariat Direktorat Jenderal');
        $ses->setTipe('KPDJP');
        $ses->setKlasifikasi(1);
        $ses->setLegacyId(2);
        $ses->setLegacyKode(2);
        $manager->persist($ses);

        $manager->flush();

        $this->addReference(self::KEMENTERIAN, $kementerian);
        $this->addReference(self::DJP, $djp);
        $this->addReference(self::SES, $ses);
    }
}

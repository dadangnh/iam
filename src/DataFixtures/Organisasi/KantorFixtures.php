<?php

namespace App\DataFixtures\Organisasi;

use App\Entity\Organisasi\Kantor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class KantorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $kementerian = new Kantor();
        $kementerian->setNama('Kementerian Keuangan RI');
        $kementerian->setJenisKantor($this->getReference(JenisKantorFixtures::KEMENTERIAN));
        $kementerian->setLevel(0);
        $manager->persist($kementerian);

        $djp = new Kantor();
        $djp->setNama('Direktorat Jenderal Pajak');
        $djp->setJenisKantor($this->getReference(JenisKantorFixtures::DJP));
        $djp->setLevel(1);
        $djp->setParentId($kementerian);
        $manager->persist($djp);

        $ses = new Kantor();
        $ses->setNama('Sekretariat Direktorat Jenderal Pajak');
        $ses->setJenisKantor($this->getReference(JenisKantorFixtures::SES));
        $ses->setLevel(2);
        $ses->setParentId($djp);
        $manager->persist($ses);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            JenisKantorFixtures::class
        ];
    }
}

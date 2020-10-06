<?php

namespace App\DataFixtures\Organisasi;

use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\GroupJabatan;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanAtribut;
use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrganisasiFixtures extends Fixture
{
    public const JABATAN_MENTERI = 'jabatan-menteri';
    public const TIPE_DEFINITIF = 'tipe-definitif';
    public const KANTOR_KEMENTERIAN = 'kantor-kementerian';
    public const UNIT_KEMENTERIAN = 'unit-kementerian';

    public function load(ObjectManager $manager)
    {
        $eselonMenteri = new Eselon();
        $eselonMenteri->setNama('Menteri');
        $eselonMenteri->setTingkat(0);
        $eselonMenteri->setKode('Menteri');
        $eselonMenteri->setLegacyKode(0);
        $manager->persist($eselonMenteri);

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

        for ($i = 1; $i <= 100; $i++) {
            $pelaksana = new JabatanAtribut();
            $pelaksana->setNama('Pelaksana ' . $i);
            $manager->persist($pelaksana);

            $ar = new JabatanAtribut();
            $ar->setNama('Account Representative ' . $i);
            $manager->persist($ar);

            $pk = new JabatanAtribut();
            $pk->setNama('Penelaah Keberatan ' . $i);
            $manager->persist($pk);

            $js = new JabatanAtribut();
            $js->setNama('Juru Sita Pajak ' . $i);
            $manager->persist($js);
        }

        $jenisKementerian = new JenisKantor();
        $jenisKementerian->setNama('Kementerian');
        $jenisKementerian->setTipe('Kementerian');
        $jenisKementerian->setKlasifikasi(1);
        $jenisKementerian->setLegacyId(0);
        $jenisKementerian->setLegacyKode(0);
        $manager->persist($jenisKementerian);

        $jenisDjp = new JenisKantor();
        $jenisDjp->setNama('Direktorat Jenderal Pajak');
        $jenisDjp->setTipe('KPDJP');
        $jenisDjp->setKlasifikasi(1);
        $jenisDjp->setLegacyId(1);
        $jenisDjp->setLegacyKode(1);
        $manager->persist($jenisDjp);

        $jenisSes = new JenisKantor();
        $jenisSes->setNama('Sekretariat Direktorat Jenderal');
        $jenisSes->setTipe('KPDJP');
        $jenisSes->setKlasifikasi(1);
        $jenisSes->setLegacyId(2);
        $jenisSes->setLegacyKode(2);
        $manager->persist($jenisSes);

        $definitif = new TipeJabatan();
        $definitif->setNama('Definitif');
        $manager->persist($definitif);

        $plh = new TipeJabatan();
        $plh->setNama('Plh/pjs');
        $manager->persist($plh);

        $adhoc = new TipeJabatan();
        $adhoc->setNama('Adhoc');
        $manager->persist($adhoc);

        $kantorKementerian = new Kantor();
        $kantorKementerian->setNama('Kementerian Keuangan RI');
        $kantorKementerian->setJenisKantor($jenisKementerian);
        $kantorKementerian->setLevel(0);
        $manager->persist($kantorKementerian);

        $kantorDjp = new Kantor();
        $kantorDjp->setNama('Direktorat Jenderal Pajak');
        $kantorDjp->setJenisKantor($jenisDjp);
        $kantorDjp->setLevel(1);
        $kantorDjp->setParentId($kantorKementerian);
        $manager->persist($kantorDjp);

        $kantorSes = new Kantor();
        $kantorSes->setNama('Sekretariat Direktorat Jenderal Pajak');
        $kantorSes->setJenisKantor($jenisSes);
        $kantorSes->setLevel(2);
        $kantorSes->setParentId($kantorDjp);
        $manager->persist($kantorSes);

        $unitKementerian = new Unit();
        $unitKementerian->setNama('Kementerian');
        $unitKementerian->setJenisKantor($jenisKementerian);
        $unitKementerian->setEselon($eselonMenteri);
        $unitKementerian->setLevel(0);
        $manager->persist($unitKementerian);

        $jabatanMenteri = new Jabatan();
        $jabatanMenteri->setNama('Menteri');
        $jabatanMenteri->setLevel(0);
        $jabatanMenteri->setJenis('STRUKTURAL');
        $jabatanMenteri->setEselon($eselonMenteri);
        $manager->persist($jabatanMenteri);

        $manager->flush();

        $this->addReference(self::JABATAN_MENTERI, $jabatanMenteri);
        $this->addReference(self::TIPE_DEFINITIF, $definitif);
        $this->addReference(self::KANTOR_KEMENTERIAN, $kantorKementerian);
        $this->addReference(self::UNIT_KEMENTERIAN, $unitKementerian);
    }
}

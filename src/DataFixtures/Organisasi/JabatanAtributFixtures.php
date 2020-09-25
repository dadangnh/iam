<?php

namespace App\DataFixtures\Organisasi;

use App\Entity\Organisasi\JabatanAtribut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JabatanAtributFixtures extends Fixture
{
    public const PELAKSANA = 'pelaksana';
    public const AR = 'ar';
    public const PK = 'pk';
    public const JS = 'juru-sita';

    public function load(ObjectManager $manager)
    {
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

            if (1 === $i) {
                $this->addReference(self::PELAKSANA, $pelaksana);
                $this->addReference(self::AR, $ar);
                $this->addReference(self::PK, $pk);
                $this->addReference(self::JS, $js);
            }
        }

        $manager->flush();
    }
}

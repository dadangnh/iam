<?php

namespace App\Controller\Admin\Pegawai;

use App\Entity\Pegawai\JenisKelamin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JenisKelaminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JenisKelamin::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(JenisKelamin::class);
        $getMaxId   = $repository->findMaxLegacyCode();
        $maxId      = $getMaxId['maxid'];

        return [
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            IntegerField::new('legacyKode','Kode')
                ->setRequired(true)
                ->setHelp(empty($maxId)?'':'Legacy Code terakhir yaitu : <span style=color:red;><i>'.$maxId.'</i></span>')
        ];
    }
}

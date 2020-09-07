<?php

namespace App\Controller\Admin\Pegawai;

use App\Entity\Pegawai\Pegawai;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PegawaiCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pegawai::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

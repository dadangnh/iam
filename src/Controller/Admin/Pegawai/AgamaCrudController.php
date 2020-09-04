<?php

namespace App\Controller\Admin\Pegawai;

use App\Entity\Pegawai\Agama;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AgamaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agama::class;
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

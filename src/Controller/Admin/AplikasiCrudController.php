<?php

namespace App\Controller\Admin;

use App\Entity\Aplikasi\Aplikasi;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AplikasiCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Aplikasi::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Aplikasi')->setIcon('fa fa-desktop'),
            TextField::new('nama')
                ->setHelp('Masukkan nama aplikasi')
                ->setLabel('Nama Aplikasi'),
            TextField::new('systemName')
                ->setHelp('Auto generated'),
            TextEditorField::new('deskripsi')
                ->setHelp('Masukkan informasi terkait aplikasi'),
            BooleanField::new('status', 'Status Aplikasi'),
            DateTimeField::new('createDate')
                ->hideOnForm(),
        ];
    }

}

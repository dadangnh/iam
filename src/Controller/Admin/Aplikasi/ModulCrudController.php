<?php

namespace App\Controller\Admin\Aplikasi;

use App\Entity\Aplikasi\Modul;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ModulCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Modul::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Aplikasi')
                ->setIcon('fa fa-desktop'),
            AssociationField::new('aplikasi')
                ->setRequired(true)
                ->setHelp('Silakan pilih nama aplikasi induk dari modul yang akan dibuat'),
            FormField::addPanel('Modul')
                ->setIcon('fa fa-code'),
            TextField::new('nama', 'Nama Modul')
                ->setRequired(true)
                ->setMaxLength(255),
            SlugField::new('systemName', 'Nama System')
                ->setRequired(true)
                ->setTargetFieldName('nama'),
            TextEditorField::new('deskripsi', 'Deskripsi Modul'),
            BooleanField::new('status', 'Status Produksi'),
            DateTimeField::new('createDate', 'Tanggal Dibuat')
                ->hideOnForm()
                ->renderAsNativeWidget(true)
        ];
    }
}

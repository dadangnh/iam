<?php

namespace App\Controller\Admin\Aplikasi;

use App\Entity\Aplikasi\Aplikasi;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
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
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255)
                ->setHelp('Nama modul, misal: HRIS'),
            SlugField::new('systemName', 'Nama System')
                ->setTargetFieldName('nama')
                ->setRequired(true)
                ->setHelp('auto generated, buka kunci untuk custom'),
            TextEditorField::new('deskripsi', 'Deskripsi')
                ->setHelp('Deskripsi/ penjelasan aplikasi'),
            TextField::new('hostName', 'Hostname')
                ->setRequired(false)
                ->setMaxLength(255)
                ->setHelp('hostname system, misal: hris.pajak.go.id'),
            TextField::new('url', 'URL')
                ->setRequired(false)
                ->setMaxLength(255)
                ->setHelp('Url aplikasi, misal: https://www.pajak.go.id'),
            BooleanField::new('status', 'Status Produksi')
                ->setHelp('Centang apabila statusnya production'),
            DateTimeField::new('createDate', 'Tanggal Dibuat')
                ->hideOnForm()
                ->renderAsNativeWidget(),
            AssociationField::new('moduls', 'Jumlah modul')
                ->hideOnForm()
        ];
    }

}

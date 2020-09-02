<?php

namespace App\Controller\Admin\Aplikasi;

use App\Entity\Aplikasi\Aplikasi;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
            TextField::new('nama', 'Nama Aplikasi')->setRequired(true),
            SlugField::new('systemName', null)->setTargetFieldName('nama')->setRequired(true),
            TextEditorField::new('deskripsi', 'Deskripsi Aplikasi'),
            BooleanField::new('status', 'Status Produksi'),
            DateTimeField::new('createDate', 'Tanggal Dibuat')
                ->hideOnForm()
                ->renderAsNativeWidget()
        ];
    }

}

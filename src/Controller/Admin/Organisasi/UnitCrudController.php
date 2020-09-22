<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\Unit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UnitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Unit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            AssociationField::new('jenisKantor', 'Jenis Kantor')
                ->setRequired(true)
                ->setHelp('Jenis Kantor dimana unit organisasi ini ada'),
            IntegerField::new('level', 'Level')
                ->setRequired(true)
                ->setHelp('
                    Level Unit Organisasi, <br>misal: Kementerian Keuangan = 0, DJP = 1, Kanwil Modern = 2, dst
                '),
            AssociationField::new('eselon', 'Eselon')
                ->setRequired(true)
                ->setHelp('Eselon tertinggi yang menjabat di unit organisasi ini'),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->setRequired(true)
                ->renderAsNativeWidget(true),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Non aktif')
                ->renderAsNativeWidget(true)
                ->hideOnIndex(),
            TextField::new('legacyKode', 'Kode Unit Lama di SIKKA')
                ->setMaxLength(10),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

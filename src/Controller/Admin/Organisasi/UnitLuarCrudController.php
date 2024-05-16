<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\UnitLuar;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UnitLuarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UnitLuar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $_pageName = $pageName;
        return [
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            AssociationField::new('jenisKantorLuar', 'Jenis Kantor Luar')
                ->setRequired(true)
                ->autocomplete()
                ->setHelp('Jenis Kantor dimana unit organisasi ini ada'),
            IntegerField::new('level', 'Level')
                ->setRequired(true)
                ->setHelp('
                    Level Unit Organisasi, <br>misal: Kementerian Keuangan = 0, DJP = 1, Kanwil Modern = 2, dst
                '),
            AssociationField::new('parent', 'Unit Induk')
                ->autocomplete(),
            AssociationField::new('childs', 'Unit Dibawahnya')
                ->onlyOnDetail(),
            AssociationField::new('eselon', 'Eselon')
                ->setRequired(true)
                ->autocomplete()
                ->setHelp('Eselon tertinggi yang menjabat di unit organisasi ini'),
            AssociationField::new('pembina', 'Unit Induk Pembina')
                ->autocomplete(),
            AssociationField::new('membina', 'Unit yang Dibina')
                ->onlyOnDetail(),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->setRequired(true)
                ->renderAsNativeWidget(),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Non aktif')
                ->renderAsNativeWidget()
                ->hideOnIndex(),
            TextField::new('legacyKode', 'Kode Unit Lama di SIKKA')
                ->setMaxLength(10),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail(),
        ];
    }
}

<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\JenisKantorLuar;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JenisKantorLuarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JenisKantorLuar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setMaxLength(255)
                ->setRequired(true),
            TextField::new('tipe', 'Tipe')
                ->setMaxLength(255)
                ->setRequired(true),
            ChoiceField::new('klasifikasi', 'Klasifikasi')
                ->allowMultipleChoices(false)
                ->setChoices([
                    '1' => '1',
                    '2' => '2'
                ])
                ->setRequired(true)
                ->hideOnIndex(),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->renderAsNativeWidget()
                ->setRequired(true),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Non aktif')
                ->renderAsNativeWidget(),
            IntegerField::new('legacyId', 'ID pada DB SIKKA Lama')
                ->hideOnIndex(),
            IntegerField::new('legacyKode', 'Kodifikasi pada DB SIKKA Lama')
                ->hideOnIndex(),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

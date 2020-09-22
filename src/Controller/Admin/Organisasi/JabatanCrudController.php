<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\Jabatan;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JabatanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Jabatan::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            IntegerField::new('level', 'Level')
                ->setRequired(true)
                ->setHelp('
                    Masukkan level jabatan,
                    <br>untuk jabatan struktural, diisi dengan level sesuai level pada eselon/ unit organisasi
                '),
            ChoiceField::new('jenis', 'Jenis')
                ->setRequired(true)
                ->setChoices([
                    'Jabatan Struktural' => 'STRUKTURAL',
                    'Jabatan Fungsional' => 'FUNGSIONAL',
                    'Jabatan Ad-hoc' => 'ADHOC'
                ]),
            AssociationField::new('eselon', 'Eselon')
                ->setHelp('Untuk jabatan struktural, isi eselon'),
            AssociationField::new('groupJabatan', 'Group Jabatan')
                ->setHelp('Pilih group jabatannya (normalnya untuk fungsional)'),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->setRequired(true)
                ->renderAsNativeWidget(true),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Nonaktif')
                ->renderAsNativeWidget(true)
                ->hideOnIndex(),
            TextField::new('sk', 'SK')
                ->setMaxLength(255)
                ->hideOnIndex(),
            TextField::new('legacyKode', 'Kode Jabatan di SIKKA Lama')
                ->setMaxLength(4)
                ->hideOnIndex(),
            TextField::new('legacyKodeJabKeu', 'Kode Jabatan Keuangan di SIKKA Lama')
                ->setMaxLength(4)
                ->hideOnIndex(),
            TextField::new('legacyKodeGradeKeu', 'Kode Jabatan Grade di SIKKA Lama')
                ->setMaxLength(4)
                ->hideOnIndex(),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

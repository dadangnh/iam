<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\JabatanLuar;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JabatanLuarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JabatanLuar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $_pageName = $pageName;
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
                ->autocomplete()
                ->setHelp('Untuk jabatan struktural, isi eselon')
                ->setRequired(false),
            AssociationField::new('GroupJabatanLuar', 'Group Jabatan')
                ->autocomplete()
                ->setHelp('Pilih group jabatannya (normalnya untuk fungsional)')
                ->setRequired(false),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->setRequired(true)
                ->renderAsNativeWidget(),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Nonaktif')
                ->renderAsNativeWidget()
                ->hideOnIndex(),
            TextField::new('sk', 'SK')
                ->setMaxLength(255)
                ->hideOnIndex(),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

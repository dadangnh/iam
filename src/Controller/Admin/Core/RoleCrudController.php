<?php

namespace App\Controller\Admin\Core;

use App\Entity\Core\Role;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Role::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255)
                ->setHelp('Nama role yang akan disimpan, diawali dengan: ROLE_'),
            SlugField::new('systemName', 'System Name')
                ->setRequired(true)
                ->setTargetFieldName('nama')->setFormattedValue(function ($value) {
                    return strtoupper($value);
                }),
            TextEditorField::new('deskripsi', 'Deskripsi'),
            IntegerField::new('level', 'Level ROLE')->setHelp('Silakan isikan nomor level ROLE'),
            AssociationField::new('subsOfRole', 'Parent Role'),
            ChoiceField::new('jenis', 'Jenis Relasi Role')
                ->setChoices([
                    'User' => 1,
                    'Jabatan' => 2,
                    'Unit' => 3,
                    'Kantor' => 4,
                    'Eselon' => 5,
                    'Jenis Kantor' => 6,
                    'Group' => 7,
                    'Jabatan + Unit' => 8,
                    'Jabatan + Kantor' => 9,
                    'Jabatan + Unit + Kantor' => 10,
                    'Jabatan + Unit + Jenis Kantor' => 11
                ])
                ->setRequired(true),
            AssociationField::new('users', 'Users')
                ->setHelp('Wajib diisi apabila jenis role = User')
                ->hideOnIndex(),
            AssociationField::new('groups', 'Groups')
                ->setHelp('Wajib diisi apabila jenis role = Groups')
                ->hideOnIndex(),
            AssociationField::new('jabatans', 'Jabatan')
                ->setHelp('
                    Wajib diisi apabila jenis role = jabatan/ jabatan + unit/ jabatan + kantor/
                    jabatan + unit + kantor /  jabatan + unit + jenis kantor
                ')
                ->hideOnIndex(),
            AssociationField::new('units', 'Unit Organisasi')
                ->setHelp('
                    Wajib diisi apabila jenis role = unit/ jabatan + unit/ jabatan + unit + kantor/
                    jabatan + unit + jenis kantor
                ')
                ->hideOnIndex(),
            AssociationField::new('kantors', 'Kantors')
                ->setHelp('
                    Wajib diisi apabila jenis role = kantor/  jabatan + kantor/ jabatan + unit + kantor
                ')
                ->hideOnIndex(),
            AssociationField::new('eselons', 'Eselon')
                ->setHelp('
                    Wajib diisi apabila jenis role = eselon
                ')
                ->hideOnIndex(),
            AssociationField::new('jenisKantors', 'Jenis Kantor')
                ->setHelp('
                    Wajib diisi apabila jenis role = jenis kantor/ jabatan + unit + jenis kantor
                ')
                ->hideOnIndex(),
        ];
    }
}

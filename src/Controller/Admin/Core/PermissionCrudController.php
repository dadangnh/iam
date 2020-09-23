<?php

namespace App\Controller\Admin\Core;

use App\Entity\Core\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PermissionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Permission::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Modul'),
            AssociationField::new('modul', 'Modul')
                ->setRequired(true)
                ->setHelp('Masukkan nama modul yang akan memiliki permission ini'),
            FormField::addPanel('Permission'),
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            SlugField::new('systemName')
                ->setTargetFieldName('nama')
                ->setRequired(true),
            TextEditorField::new('deskripsi'),
            FormField::addPanel('Role'),
            AssociationField::new('roles', 'Role')
                ->setHelp('Masukkan nama ROLE yang dapat menggunakan permission ini')
        ];
    }
}

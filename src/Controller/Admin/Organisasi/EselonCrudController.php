<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\Eselon;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EselonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Eselon::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            IntegerField::new('tingkat', 'Tingkat')->setRequired(true)
                ->setHelp('Masukkan tingkat eselon: misal: Menteri = 0, Dirjen = 1, dst'),
            TextField::new('kode', 'Kode Penulisan Eselon')
                ->setRequired(true)
                ->setMaxLength(255)
                ->setHelp('Masukkan Kode sesuai ketentuan, misal Eselon 1a = I.a, Eselon 4b = IV.b'),
            IntegerField::new('legacyKode', 'Kode Eselon lama di SIKKA'),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

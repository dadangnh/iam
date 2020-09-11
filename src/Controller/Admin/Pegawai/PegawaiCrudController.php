<?php

namespace App\Controller\Admin\Pegawai;

use App\Entity\Pegawai\Pegawai;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PegawaiCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pegawai::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama Pegawai')
                ->setRequired(true)
                ->setMaxLength(255),
            AssociationField::new('user')
                ->setRequired(true)
                ->setHelp('Silakan pilih nama user yang telah didaftarkan'),
            DateField::new('tanggalLahir', 'Tanggal Lahir')
                ->renderAsNativeWidget(true),
            TextField::new('tempatLahir', 'Tempat lahir')
                ->setRequired(true)
                ->setMaxLength(255),
            AssociationField::new('jenisKelamin')
                ->setRequired(true)
                ->setHelp('Silakan pilih salah satu'),
            AssociationField::new('agama')
                ->setRequired(true)
                ->setHelp('Silakan pilih salah satu'),
            TextField::new('npwp','NPWP'),
            TextField::new('nik','NIK')
                ->setRequired(true)
                ->setMaxLength(15),
            TextField::new('nip9','IP SIKKA')
                ->setRequired(true)
                ->setMaxLength(9),
        ];
    }
}

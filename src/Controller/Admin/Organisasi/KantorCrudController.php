<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\Kantor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class KantorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Kantor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setMaxLength(255),
            AssociationField::new('jenisKantor', 'Jenis Kantor')
                ->setRequired(true),
            IntegerField::new('Level', 'Level')
                ->setHelp('Masukkan level sesuai tingkatan kantor,
                    <br>misal: Kementerian = 0, DJP = 1, Kanwil = 2, dst
                ')
                ->hideOnIndex(),
            AssociationField::new('parentId', 'Kantor Induk'),
            AssociationField::new('childIds', 'Kantor Dibawahnya')
                ->onlyOnDetail(),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->renderAsNativeWidget(true),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Non Aktif')
                ->renderAsNativeWidget(true),
            TextField::new('sk', 'Nomor SK Pengaktifan')
                ->hideOnIndex()
                ->setMaxLength(255),
            TextEditorField::new('alamat', 'Alamat')
                ->hideOnIndex(),
            TelephoneField::new('telp', 'Nomor Telpon')
                ->hideOnIndex(),
            TelephoneField::new('fax', 'Nomor Fax')
                ->hideOnIndex(),
            ChoiceField::new('zonaWaktu', 'Zona Waktu')
                ->setChoices([
                    'Waktu Indonesia Bagian Barat' => 'WIB',
                    'Waktu Indonesia Bagian Tengah' => 'WITA',
                    'Waktu Indonesia Bagian Timur' => 'WIT'
                ])
                ->hideOnIndex(),
            NumberField::new('latitude', 'Latitude')
                ->setNumDecimals(10),
            NumberField::new('longitude', 'Longitude')
                ->setNumDecimals(10),
            TextField::new('legacyKode', 'Kode Kantor Lama')
                ->hideOnIndex()
                ->setMaxLength(10),
            TextField::new('legacyKodeKpp', 'Kode KPP lama di SIDJP')
                ->hideOnIndex()
                ->setMaxLength(3),
            TextField::new('legacyKodeKanwil', 'Kode Kanwil di SIDJP')
                ->hideOnIndex()
                ->setMaxLength(3),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

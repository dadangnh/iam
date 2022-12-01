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
                ->setRequired(true)
                ->autocomplete(),
            IntegerField::new('Level', 'Level')
                ->setHelp('Masukkan level sesuai tingkatan kantor,
                    <br>misal: Kementerian = 0, DJP = 1, Kanwil = 2, dst
                ')
                ->hideOnIndex(),
            AssociationField::new('parent', 'Kantor Induk')
                ->autocomplete(),
            AssociationField::new('childs', 'Kantor Dibawahnya')
                ->onlyOnDetail(),
            AssociationField::new('pembina', 'Kantor Induk Pembina')
                ->autocomplete(),
            AssociationField::new('membina', 'Kantor yang Dibina')
                ->onlyOnDetail(),
            DateTimeField::new('tanggalAktif', 'Tanggal Aktif')
                ->renderAsNativeWidget(),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Non Aktif')
                ->renderAsNativeWidget(),
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
            TextField::new('ministryOfficeCode', 'Kode Kantor di HRIS Kementerian')
                ->hideOnIndex()
                ->setMaxLength(10),
            TextField::new('provinsi', 'Provinsi Id')
                ->hideOnIndex(),
            TextField::new('kabupatenKota', 'Kabupaten Kota Id')
                ->hideOnIndex(),
            TextField::new('kecamatan', 'Kecamatan Id')
                ->hideOnIndex(),
            TextField::new('kelurahan', 'Kelurahan Id')
                ->hideOnIndex(),
            TextField::new('provinsiName', 'Provinsi Name')
                ->hideOnIndex(),
            TextField::new('kabupatenKotaName', 'Kabupaten Kota Name')
                ->hideOnIndex(),
            TextField::new('kecamatanName', 'Kecamatan Name')
                ->hideOnIndex(),
            TextField::new('kelurahanName', 'Kelurahan Name')
                ->hideOnIndex(),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

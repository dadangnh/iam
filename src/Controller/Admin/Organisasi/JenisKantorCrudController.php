<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\JenisKantor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JenisKantorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JenisKantor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nama', 'Nama')
                ->setMaxLength(255)
                ->setRequired(true),
            ChoiceField::new('tipe', 'Tipe')
                ->allowMultipleChoices(false)
                ->setChoices([
                    'Kementerian' => 'KEMENTERIAN',
                    'Kantor Pusat DJP' => 'KPDJP',
                    'UPT' => 'UPT',
                    'Kantor Wilayah' => 'KANWIL',
                    'Kantor Pajak Pratama' => 'KPP'
                ])
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
                ->renderAsNativeWidget(true)
                ->setRequired(true),
            DateTimeField::new('tanggalNonaktif', 'Tanggal Non aktif')
                ->renderAsNativeWidget(true),
            IntegerField::new('legacyId', 'ID pada DB SIKKA Lama')
                ->hideOnIndex(),
            IntegerField::new('legacyKode', 'Kodifikasi pada DB SIKKA Lama')
                ->hideOnIndex(),
            AssociationField::new('roles', 'Roles')
                ->onlyOnDetail()
        ];
    }
}

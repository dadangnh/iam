<?php

namespace App\Controller\Admin\Pegawai;

use App\Entity\Pegawai\Agama;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AgamaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agama::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Agama::class);
        $maxId      = $repository->findMaxLegacyCode();

        return[
            TextField::new('nama', 'Nama')
                ->setRequired(true)
                ->setMaxLength(255),
            IntegerField::new('legacyKode','Kode')
                ->setRequired(true)
                ->setHelp(
                    empty($maxId)
                        ? ''
                        : 'Legacy Code terakhir yaitu : <span style=color:red;><i>' . $maxId . '</i></span>'
                )
        ];
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

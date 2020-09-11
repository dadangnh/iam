<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\JabatanAtribut;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class JabatanAtributCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JabatanAtribut::class;
    }
}

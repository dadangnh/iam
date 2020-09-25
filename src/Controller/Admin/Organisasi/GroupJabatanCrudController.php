<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\GroupJabatan;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GroupJabatanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupJabatan::class;
    }
}

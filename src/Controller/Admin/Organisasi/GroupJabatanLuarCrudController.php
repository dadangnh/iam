<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\GroupJabatanLuar;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GroupJabatanLuarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupJabatanLuar::class;
    }
}

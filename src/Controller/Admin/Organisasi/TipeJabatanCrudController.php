<?php

namespace App\Controller\Admin\Organisasi;

use App\Entity\Organisasi\TipeJabatan;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TipeJabatanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TipeJabatan::class;
    }
}

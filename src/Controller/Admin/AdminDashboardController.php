<?php

namespace App\Controller\Admin;

use App\Entity\Aplikasi\Aplikasi;
use App\Entity\Aplikasi\Modul;
use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Iam');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Aplikasi');
        yield MenuItem::linkToCrud('Aplikasi', 'fa fa-desktop', Aplikasi::class);
        yield MenuItem::linkToCrud('Modul', 'fa fa-code', Modul::class);
        yield MenuItem::section('Organisasi');
        yield MenuItem::linkToCrud('Jenis Kantor', 'fa fa-university', JenisKantor::class);
        yield MenuItem::linkToCrud('Kantor', 'fa fa-building', Kantor::class);
        yield MenuItem::linkToCrud('Eselon', 'fa fa-signal', Eselon::class);
        // yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);
    }
}

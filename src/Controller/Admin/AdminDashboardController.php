<?php

namespace App\Controller\Admin;

use App\Entity\Aplikasi\Aplikasi;
use App\Entity\Aplikasi\Modul;
use App\Entity\Core\Permission;
use App\Entity\Core\Role;
use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\GroupJabatan;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanAtribut;
use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Entity\Pegawai\Agama;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\JenisKelamin;
use App\Entity\Pegawai\Pegawai;
use App\Entity\User\User;
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
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DJP<i>connect</i>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Aplikasi');
        yield MenuItem::linkToCrud('Aplikasi', 'fa fa-desktop', Aplikasi::class);
        yield MenuItem::linkToCrud('Modul', 'fa fa-code', Modul::class);
        yield MenuItem::section('Organisasi');
        yield MenuItem::linkToCrud('Jenis Kantor', 'fa fa-university', JenisKantor::class);
        yield MenuItem::linkToCrud('Eselon', 'fa fa-signal', Eselon::class);
        yield MenuItem::linkToCrud('Tipe Jabatan', 'fa fa-ticket', TipeJabatan::class);
        yield MenuItem::linkToCrud('Group Jabatan', 'fa fa-users', GroupJabatan::class);
        yield MenuItem::linkToCrud('Jabatan Atribut', 'fa fa-vcard', JabatanAtribut::class);
        yield MenuItem::linkToCrud('Kantor', 'fa fa-building', Kantor::class);
        yield MenuItem::linkToCrud('Unit Organisasi', 'fa fa-building-o', Unit::class);
        yield MenuItem::linkToCrud('Jabatan', 'fa fa-id-card', Jabatan::class);
        yield MenuItem::section('Pegawai');
        yield MenuItem::linkToCrud('Agama', 'fa fa-flag', Agama::class) ;
        yield MenuItem::linkToCrud('Jenis Kelamin', 'fas fa-venus-mars', JenisKelamin::class);
        yield MenuItem::linkToCrud('Pegawai','far fa-address-book',Pegawai::class);
        yield MenuItem::linkToCrud('Jabatan Pegawai','far fa-id-card',JabatanPegawai::class);
        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class) ;
        yield MenuItem::section('Core');
        yield MenuItem::linkToCrud('Role', 'fa fa-user-secret', Role::class) ;
        yield MenuItem::linkToCrud('Permissions', 'fa fa-terminal', Permission::class) ;
    }
}

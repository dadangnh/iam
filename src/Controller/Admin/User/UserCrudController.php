<?php

namespace App\Controller\Admin\User;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username', 'User Name')
                ->setRequired(true),
            TextField::new('plainPassword', 'Password')
                ->setRequired(true)
                ->setFormType(PasswordType::class)
                ->onlyOnForms(),
            AssociationField::new('role')
                ->onlyOnDetail(),
            BooleanField::new('active', 'Akun aktif?'),
            BooleanField::new('locked', 'Akun terkunci?'),
            BooleanField::new('twoFactorEnabled', '2FA')
        ];
    }
}

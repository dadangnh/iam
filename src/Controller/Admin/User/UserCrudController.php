<?php

namespace App\Controller\Admin\User;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username', 'Username'),
            TextField::new('password', 'Password')
                ->onlyOnForms()
                ->setFormType(PasswordType::class),
            BooleanField::new('status', 'Status'),
            BooleanField::new('locked', 'locked'),
            BooleanField::new('twoFactorEnabled', '2FA')
                ->hideOnForm(),
            DateTimeField::new('lastChange', 'Last change')
                ->hideOnForm()
                ->renderAsNativeWidget()
        ];
    }
}

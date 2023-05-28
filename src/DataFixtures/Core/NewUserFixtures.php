<?php

namespace App\DataFixtures\Core;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NewUserFixtures extends Fixture
{
    public const USER_SUPER_ADMIN = 'super';
    public const USER_ADMIN = 'admin';
    public const USER_UPK_PUSAT = 'upk';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create ADMIN ROLE
        $superAdmin = new User();
        $superAdmin->setUsername('root');
        $superAdmin->setPassword($this->passwordHasher->hashPassword(
            $superAdmin,
            'toor'
        ));
        $superAdmin->setActive(true);
        $superAdmin->setLocked(false);
        $superAdmin->setTwoFactorEnabled(false);
        $manager->persist($superAdmin);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'admin'
        ));
        $admin->setActive(true);
        $admin->setLocked(false);
        $admin->setTwoFactorEnabled(false);
        $manager->persist($admin);

        $upkPusat = new User();
        $upkPusat->setUsername('upk_pusat');
        $upkPusat->setPassword($this->passwordHasher->hashPassword(
            $upkPusat,
            'upk_pusat'
        ));
        $upkPusat->setActive(true);
        $upkPusat->setLocked(false);
        $upkPusat->setTwoFactorEnabled(false);
        $manager->persist($upkPusat);

        $manager->flush();
        $this->addReference(self::USER_SUPER_ADMIN, $superAdmin);
        $this->addReference(self::USER_ADMIN, $admin);
        $this->addReference(self::USER_UPK_PUSAT, $upkPusat);
    }
}

<?php

namespace App\DataFixtures\User;

use App\DataFixtures\Core\RoleFixtures;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtures extends Fixture
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager)
    {
        // Create ADMIN ROLE
        $superAdmin = new User();
        $superAdmin->setUsername('root');
        $superAdmin->setPassword(
            $this->encoderFactory->getEncoder(User::class)->encodePassword('toor', null)
        );
        $superAdmin->addRole($this->getReference(RoleFixtures::ROLE_SUPER_ADMIN));
        $superAdmin->addRole($this->getReference(RoleFixtures::ROLE_ADMIN));
        $superAdmin->setStatus(true);
        $superAdmin->setLocked(false);
        $superAdmin->setTwoFactorEnabled(false);
        $manager->persist($superAdmin);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword(
            $this->encoderFactory->getEncoder(User::class)->encodePassword('admin', null)
        );
        $admin->addRole($this->getReference(RoleFixtures::ROLE_ADMIN));
        $admin->setStatus(true);
        $admin->setLocked(false);
        $admin->setTwoFactorEnabled(false);
        $manager->persist($admin);

        $upkPusat = new User();
        $upkPusat->setUsername('upk_pusat');
        $upkPusat->setPassword(
            $this->encoderFactory->getEncoder(User::class)->encodePassword('upk_pusat', null)
        );
        $upkPusat->addRole($this->getReference(RoleFixtures::ROLE_UPK_PUSAT));
        $upkPusat->setStatus(true);
        $upkPusat->setLocked(false);
        $upkPusat->setTwoFactorEnabled(false);
        $manager->persist($upkPusat);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            RoleFixtures::class
        ];
    }
}

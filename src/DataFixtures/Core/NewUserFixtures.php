<?php

namespace App\DataFixtures\Core;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class NewUserFixtures extends Fixture
{
    public const USER_SUPER_ADMIN = 'super';
    public const USER_ADMIN = 'admin';
    public const USER_UPK_PUSAT = 'upk';

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
        $superAdmin->setActive(true);
        $superAdmin->setLocked(false);
        $superAdmin->setTwoFactorEnabled(false);
        $manager->persist($superAdmin);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword(
            $this->encoderFactory->getEncoder(User::class)->encodePassword('admin', null)
        );
        $admin->setActive(true);
        $admin->setLocked(false);
        $admin->setTwoFactorEnabled(false);
        $manager->persist($admin);

        $upkPusat = new User();
        $upkPusat->setUsername('upk_pusat');
        $upkPusat->setPassword(
            $this->encoderFactory->getEncoder(User::class)->encodePassword('upk_pusat', null)
        );
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

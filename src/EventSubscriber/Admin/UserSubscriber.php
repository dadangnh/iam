<?php

namespace App\EventSubscriber\Admin;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class UserSubscriber
 * @package App\EventSubscriber\Admin
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    #[ArrayShape([BeforeEntityPersistedEvent::class => "string[]", BeforeEntityUpdatedEvent::class => "string[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setNewUserEncodedPassword'],
            BeforeEntityUpdatedEvent::class => ['setOldUserEncodedPassword'],
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function setNewUserEncodedPassword(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $this->preUpdateUserEntity($entity);
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     */
    public function setOldUserEncodedPassword(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $this->preUpdateUserEntity($entity);
    }

    /**
     * @param User $user
     */
    protected function prePersistUserEntity(User $user): void
    {
        $encodedPassword = $this->encodePassword($user);
        $user->setPassword($encodedPassword);
    }

    /**
     * @param User $user
     */
    protected function preUpdateUserEntity(User $user): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }
        $encodedPassword = $this->encodePassword($user);
        $user->setPassword($encodedPassword);
    }

    /**
     * @param User $user
     * @return string
     */
    private function encodePassword(User $user): string
    {
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder,
        ];

        $encoderFactory = new EncoderFactory($encoders);
        return $encoderFactory
            ->getEncoder($user)
            ->encodePassword(
                $user->getPlainPassword(),
                $user->getSalt()
            );
    }
}

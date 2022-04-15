<?php

namespace App\EventSubscriber\Admin;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserSubscriber
 * @package App\EventSubscriber\Admin
 */
class UserSubscriber implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {

        $this->hasher = $hasher;
    }

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
        if (!$user->getPlainPassword()) {
            return;
        }

        $newPasswordHashed = $this->hasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($newPasswordHashed);
    }

    /**
     * @param User $user
     */
    protected function preUpdateUserEntity(User $user): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $newPasswordHashed = $this->hasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($newPasswordHashed);
    }
}

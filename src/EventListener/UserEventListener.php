<?php

namespace App\EventListener;

use App\Entity\User\User;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserEventListener
{
    /**
     * @throws Exception
     */
    public function postLoad(User $user, LifecycleEventArgs $event): void
    {
        $user->generateRoles($event);
    }
}

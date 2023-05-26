<?php

namespace App\EventListener;

use App\Entity\User\User;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ManagerRegistry;

class customEventListener
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function postLoad(User $user, LifecycleEventArgs $event): void
    {
        $user->generateRoles($event);
    }
}

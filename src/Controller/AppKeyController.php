<?php


namespace App\Controller;


use App\Entity\User\User;

class AppKeyController
{
    public function __construct()
    {

    }

    public function __invoke(User $user):User
    {
        // TODO: Implement __invoke() method.
        return $user;
    }
}
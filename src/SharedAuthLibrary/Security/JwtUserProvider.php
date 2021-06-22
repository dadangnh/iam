<?php

declare(strict_types=1);

namespace App\SharedAuthLibrary\Security;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\Exception\ExceptionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function get_class;

class JwtUserProvider implements UserProviderInterface
{
    private JwtPayloadContainer $jwtPayloadContainer;

    /**
     * JwtUserProvider constructor.
     * @param JwtPayloadContainer $jwtPayloadContainer
     */
    public function __construct(JwtPayloadContainer $jwtPayloadContainer)
    {
        $this->jwtPayloadContainer = $jwtPayloadContainer;
    }

    /**
     * @param string $username
     * @return User
     */
    #[Pure] public function loadUserByUsername($username): User
    {
        $payload = $this->jwtPayloadContainer->getPayload();

        // If user is not found, throw exception
        if (empty($payload)) {
            throw new UsernameNotFoundException();
        }

        return new User(
            $payload['id'],
            $payload['username'],
            $payload['roles'],
            $payload['expired'],
            $payload['pegawai']
        );
    }

    /**
     * @param UserInterface $user
     * @return User
     */
    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}

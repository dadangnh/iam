<?php

declare(strict_types=1);

namespace App\SharedAuthLibrary\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
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
     * @param string $identifier
     * @return User
     */
    public function loadUserByIdentifier(string $identifier): User
    {
        $payload = $this->jwtPayloadContainer->getPayload();

        // If user is not found, throw exception
        if (empty($payload)) {
            throw new UserNotFoundException();
        }

        return new User(
            $payload['id'],
            $payload['username'],
            $payload['roles'],
            $payload['expired'],
            $payload['pegawai'],
            $payload['pegawaiLuar']
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

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}

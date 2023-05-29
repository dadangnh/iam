<?php

declare(strict_types=1);

namespace App\SharedAuthLibrary\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var array
     */
    private array $roles;

    /**
     * @var int
     */
    private int $expiredTime;

    /**
     * @var array|null
     */
    private ?array $pegawai;

    /**
     * User constructor.
     * @param string $id
     * @param string $username
     * @param array $roles
     * @param int $expiredTime
     * @param array|null $pegawai
     */
    public function __construct(
        string $id,
        string $username,
        array  $roles,
        int    $expiredTime,
        ?array $pegawai,
    )
    {
        $this->id = $id;
        $this->roles = $roles;
        $this->username = $username;
        $this->expiredTime = $expiredTime;
        $this->pegawai = $pegawai;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return int
     */
    public function getExpiredTime(): int
    {
        return $this->expiredTime;
    }

    /**
     * @return array|null
     */
    public function getPegawai(): ?array
    {
        return $this->pegawai;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}

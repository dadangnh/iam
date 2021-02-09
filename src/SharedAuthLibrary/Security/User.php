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
     * @var string
     */
    private string $password;

    /**
     * @var string
     */
    private string $salt;

    /**
     * User constructor.
     * @param string $id
     * @param string $username
     * @param array $roles
     * @param int $expiredTime
     * @param array|null $pegawai
     * @param string $password
     * @param string $salt
     */
    public function __construct(
        string $id,
        string $username,
        array $roles,
        int $expiredTime,
        ?array $pegawai,
        string $password = '',
        string $salt = '',
    ) {
        $this->id = $id;
        $this->roles = $roles;
        $this->username = $username;
        $this->expiredTime = $expiredTime;
        $this->pegawai = $pegawai;
        $this->password = $password;
        $this->salt = $salt;
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

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
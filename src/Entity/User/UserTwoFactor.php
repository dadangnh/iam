<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\User\UserTwoFactorRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UserTwoFactorRepository::class)
 * @ORM\Table(name="user_two_factor", indexes={
 *     @ORM\Index(name="idx_user_two_factor_data", columns={"id", "tfa_type"}),
 *     @ORM\Index(name="idx_user_two_factor_relation", columns={"id", "user_id"}),
 * })
 */
class UserTwoFactor
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userTwoFactors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $backupCode = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $tfaType;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBackupCode(): ?array
    {
        return $this->backupCode;
    }

    public function setBackupCode(?array $backupCode): self
    {
        $this->backupCode = $backupCode;

        return $this;
    }

    public function getTfaType(): ?int
    {
        return $this->tfaType;
    }

    public function setTfaType(int $tfaType): self
    {
        $this->tfaType = $tfaType;

        return $this;
    }
}

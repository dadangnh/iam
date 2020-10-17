<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\User\UserTwoFactorRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *          "security"="is_granted('ROLE_USER') or is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *          "security_message"="Only a valid user/admin/app can access this."
 *     },
 *     collectionOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_USER') or is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only a valid user/admin/app can access this."
 *          },
 *         "post"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can add new resource to this entity type."
 *          }
 *     },
 *     itemOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_USER') or is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only a valid user/admin/app can access this."
 *          },
 *         "put"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can replace this entity type."
 *          },
 *         "patch"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can edit this entity type."
 *          },
 *         "delete"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can delete this entity type."
 *          },
 *     }
 * )
 * @ORM\Entity(repositoryClass=UserTwoFactorRepository::class)
 * @ORM\Table(name="user_two_factor", indexes={
 *     @ORM\Index(name="idx_user_two_factor_data", columns={"id", "tfa_type"}),
 *     @ORM\Index(name="idx_user_two_factor_relation", columns={"id", "user_id"}),
 * })
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
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
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userTwoFactors")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     */
    private $user;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $backupCode = [];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
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

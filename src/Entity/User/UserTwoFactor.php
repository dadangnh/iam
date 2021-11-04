<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\User\UserTwoFactorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserTwoFactor Class
 */
#[ORM\Entity(
    repositoryClass: UserTwoFactorRepository::class
)]
#[ORM\Table(
    name: 'user_two_factor'
)]
#[ORM\Index(
    columns: [
        'id',
        'tfa_type'
    ],
    name: 'idx_user_two_factor_data'
)]
#[ORM\Index(
    columns: [
        'id',
        'user_id'
    ],
    name: 'idx_user_two_factor_relation'
)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_USER")',
            'security_message' => 'Only a valid user can access this.'
        ],
        'post' => [
            'security'=>'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message'=>'Only admin/app can add new resource to this entity type.'
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_USER")',
            'security_message' => 'Only a valid user can access this.'
        ],
        'put' => [
            'security' => 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message' => 'Only admin/app can add new resource to this entity type.'
        ],
        'patch' => [
            'security' => 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message' => 'Only admin/app can add new resource to this entity type.'
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message' => 'Only admin/app can add new resource to this entity type.'
        ],
    ],
    attributes: [
        'security' => 'is_granted("ROLE_USER")',
        'security_message' => 'Only a valid user can access this.',
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'user.id' => 'exact',
        'user.name' => 'partial'
    ]
)]
class UserTwoFactor
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    private $id;

    #[ORM\ManyToOne(
        targetEntity: User::class, inversedBy: 'userTwoFactors'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    private $user;

    #[ORM\Column(
        type: 'json',
        nullable: true
    )]
    private ?array $backupCode = [];

    #[ORM\Column(
        type: 'integer'
    )]
    private ?int $tfaType;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
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

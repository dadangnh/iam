<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Repository\User\GroupRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Group Class
 */
#[ORM\Entity(
    repositoryClass: GroupRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: '`group`'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'system_name',
        'status'
    ],
    name: 'idx_group_data'
)]
#[ORM\Index(
    columns: [
        'id',
        'owner_id'
    ],
    name: 'idx_group_relation'
)]
#[UniqueEntity(
    fields: [
        'nama',
        'systemName'
    ]
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
        'order' => [
            'createDate' => 'ASC',
            'nama' => 'ASC'
        ]
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'systemName' => 'ipartial',
        'deskripsi' => 'ipartial',
    ]
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'createDate'
    ]
)]
#[ApiFilter(
    BooleanFilter::class,
    properties: [
        'status'
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class Group
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    private UuidV4 $id;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    private ?string $nama;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    private ?string $systemName;

    #[ORM\Column(
        type: Types::TEXT,
        nullable: true
    )]
    private ?string $deskripsi;

    #[ORM\ManyToOne(
        targetEntity: User::class,
        inversedBy: 'ownedGroups'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    private ?User $owner;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    private ?DateTimeImmutable $createDate;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    private ?bool $status;

    #[ORM\OneToMany(
        mappedBy: 'groupId',
        targetEntity: GroupMember::class,
        orphanRemoval: true
    )]
    private Collection $groupMembers;

    #[ORM\ManyToMany(
        targetEntity: Role::class,
        mappedBy: 'groups'
    )]
    private Collection $roles;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->groupMembers = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nama;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNama(): ?string
    {
        return $this->nama;
    }

    public function setNama(string $nama): self
    {
        $this->nama = $nama;

        return $this;
    }

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): self
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getDeskripsi(): ?string
    {
        return $this->deskripsi;
    }

    public function setDeskripsi(?string $deskripsi): self
    {
        $this->deskripsi = $deskripsi;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreateDate(): ?DateTimeImmutable
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeImmutable $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreateDateValue(): void
    {
        $this->createDate = new DateTimeImmutable();
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|GroupMember[]
     */
    public function getGroupMembers(): Collection|array
    {
        return $this->groupMembers;
    }

    public function addGroupMember(GroupMember $groupMember): self
    {
        if (!$this->groupMembers->contains($groupMember)) {
            $this->groupMembers[] = $groupMember;
            $groupMember->setGroupId($this);
        }

        return $this;
    }

    public function removeGroupMember(GroupMember $groupMember): self
    {
        if ($this->groupMembers->contains($groupMember)) {
            $this->groupMembers->removeElement($groupMember);
            // set the owning side to null (unless already changed)
            if ($groupMember->getGroupId() === $this) {
                $groupMember->setGroupId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection|array
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }
}

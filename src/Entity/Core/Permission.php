<?php

namespace App\Entity\Core;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Aplikasi\Modul;
use App\Repository\Core\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Permissions Class
 */
#[ORM\Entity(
    repositoryClass: PermissionRepository::class
)]
#[ORM\Table(
    name: 'permission'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'system_name'
    ],
    name: 'idx_permission_nama_status'
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
        'roles.id' => 'exact',
        'roles.nama' => 'exact',
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    private UuidV4 $id;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Assert\NotBlank]
    private ?string $nama;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Assert\NotBlank]
    private ?string $systemName;

    #[ORM\Column(
        type: 'text',
        nullable: true
    )]
    private ?string $deskripsi;

    #[ORM\ManyToMany(
        targetEntity: Modul::class,
        inversedBy: 'permissions'
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Collection $modul;

    #[ORM\ManyToMany(
        targetEntity: Role::class,
        inversedBy: 'permissions',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_permission',
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'permission_id',
        referencedColumnName: 'id'
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    private Collection $roles;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->modul = new ArrayCollection();
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

    /**
     * @return Collection|Modul[]
     */
    public function getModul(): Collection|array
    {
        return $this->modul;
    }

    public function addModul(Modul $modul): self
    {
        if (!$this->modul->contains($modul)) {
            $this->modul[] = $modul;
        }

        return $this;
    }

    public function removeModul(Modul $modul): self
    {
        if ($this->modul->contains($modul)) {
            $this->modul->removeElement($modul);
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

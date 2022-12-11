<?php

namespace App\Entity\Aplikasi;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Entity\Core\Permission;
use App\Repository\Aplikasi\ModulRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Modul Class
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_USER")',
            securityMessage: 'Only a valid user can access this.'
        ),
        new Put(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        ),
        new Patch(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        ),
        new Delete(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            securityMessage: 'Only a valid user can access this.'
        ),
        new Post(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        )
    ],
    normalizationContext: [
        'groups' => [
            'modul:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'modul:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    order: [
        'createDate' => 'ASC',
        'nama' => 'ASC'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: ModulRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'modul'
)]
#[ORM\Index(
    columns: [
        'nama',
        'system_name',
        'status'
    ],
    name: 'idx_modul_nama_status'
)]
#[ORM\Index(
    columns: [
        'aplikasi_id',
        'nama',
        'system_name'
    ],
    name: 'idx_modul_nama_aplikasi'
)]
#[UniqueEntity(
    fields: [
        'nama',
        'systemName'
    ]
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'systemName' => 'ipartial',
        'deskripsi' => 'ipartial',
        'aplikasi.id' => 'exact',
        'aplikasi.nama' => 'iexact'
    ]
)]
#[ApiFilter(
    filterClass: DateFilter::class,
    properties: [
        'createDate'
    ]
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'status',
        'aplikasi.status'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class Modul
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'modul:read',
            'modul:write'
        ]
    )]
    private UuidV4 $id;

    #[ORM\ManyToOne(
        targetEntity: Aplikasi::class,
        inversedBy: 'moduls'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'modul:read',
            'modul:write'
        ]
    )]
    private ?Aplikasi $aplikasi;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'modul:read',
            'modul:write'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'modul:read',
            'modul:write'
        ]
    )]
    private ?string $systemName;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'modul:read',
            'modul:write'
        ]
    )]
    private ?bool $status;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    private ?DateTimeImmutable $createDate;

    #[ORM\Column(
        type: Types::TEXT,
        nullable: true
    )]
    #[Groups(
        groups: [
            'modul:read',
            'modul:write'
        ]
    )]
    private ?string $deskripsi;

    #[ORM\ManyToMany(
        targetEntity: Permission::class,
        mappedBy: 'modul'
    )]
    private Collection $permissions;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->permissions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nama;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAplikasi(): ?Aplikasi
    {
        return $this->aplikasi;
    }

    public function setAplikasi(?Aplikasi $aplikasi): self
    {
        $this->aplikasi = $aplikasi;

        return $this;
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection|array
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->addModul($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            $permission->removeModul($this);
        }

        return $this;
    }
}

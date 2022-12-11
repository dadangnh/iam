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
use App\Repository\Aplikasi\AplikasiRepository;
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
 * Aplikasi Class
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
    order: [
        'createDate' => 'ASC',
        'nama' => 'ASC'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: AplikasiRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'aplikasi'
)]
#[ORM\Index(
    columns: [
        'nama',
        'system_name',
        'status'
    ],
    name: 'idx_aplikasi_nama_status'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'host_name',
        'url'
    ],
    name: 'idx_aplikasi_url'
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
        'hostName' => 'ipartial',
        'url' => 'ipartial',
        'moduls.id' => 'exact',
        'moduls.nama' => 'ipartial',
        'moduls.permissions.id' => 'exact',
        'moduls.permissions.nama' => 'exact',
        'moduls.permissions.roles.nama' => 'exact'
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
        'moduls.status'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class Aplikasi
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'modul:read'
        ]
    )]
    private UuidV4 $id;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'modul:read'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    private ?string $systemName;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    private ?bool $status;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    private ?DateTimeImmutable $createDate;

    #[ORM\Column(
        type: Types::TEXT,
        nullable: true
    )]
    private ?string $deskripsi;

    #[ORM\OneToMany(
        mappedBy: 'aplikasi',
        targetEntity: Modul::class,
        orphanRemoval: true
    )]
    private Collection $moduls;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    private ?string $hostName;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    private ?string $url;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->moduls = new ArrayCollection();
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
     * @return Collection|Modul[]
     */
    public function getModuls(): Collection|array
    {
        return $this->moduls;
    }

    public function addModul(Modul $modul): self
    {
        if (!$this->moduls->contains($modul)) {
            $this->moduls[] = $modul;
            $modul->setAplikasi($this);
        }

        return $this;
    }

    public function removeModul(Modul $modul): self
    {
        if ($this->moduls->contains($modul)) {
            $this->moduls->removeElement($modul);
            // set the owning side to null (unless already changed)
            if ($modul->getAplikasi() === $this) {
                $modul->setAplikasi(null);
            }
        }

        return $this;
    }

    public function getHostName(): ?string
    {
        return $this->hostName;
    }

    public function setHostName(?string $hostName): self
    {
        $this->hostName = $hostName;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}

<?php

namespace App\Entity\Pegawai;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Entity\User\User;
use App\Repository\Pegawai\PegawaiLuarRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pegawai Class
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
            security: 'is_granted("ROLE_USER")', securityMessage: 'Only a valid user can access this.'
        ),
        new Post(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        )
    ],
    normalizationContext: [
        'groups' => [
            'pegawai-luar:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'pegawai-luar:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: PegawaiLuarRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'pegawai_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'pensiun',
        'nik',
        'nip18',
        'pangkat'
    ],
    name: 'idx_pegawai_luar_data'
)]
#[ORM\Index(
    columns: [
        'id',
        'nip18'
    ],
    name: 'idx_pegawai_luar_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'user_luar_id'
    ],
    name: 'idx_pegawai_luar_relation'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'nip18' => 'partial',
        'user.username' => 'ipartial',
        'pangkat' => 'ipartial'
    ]
)]
#[ApiFilter(
    filterClass: DateFilter::class,
    properties: [
        'tanggalLahir'
    ]
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'pensiun'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class PegawaiLuar
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private UuidV4 $id;
    #[ORM\OneToOne(
        inversedBy: 'pegawaiLuar',
        cascade: ['persist', 'remove']
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?User $userLuar = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'user:read',
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?bool $pensiun;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?string $npwp;

    #[ORM\Column(
        type: Types::STRING,
        length: 16,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?string $nik;

    #[ORM\Column(
        type: Types::STRING,
        length: 18,
        nullable: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?string $nip18;

    #[ORM\OneToMany(
        mappedBy: 'pegawaiLuar',
        targetEntity: JabatanPegawaiLuar::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private Collection $jabatanPegawaiLuars;

    #[Groups(
        groups: [
            'user:read',
            'pegawai-luar:read'
        ]
    )]
    private array $activePositionIds;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read',
            'pegawai-luar:write'
        ]
    )]
    private ?string $pangkat;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->jabatanPegawaiLuars = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nama;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserLuar(): ?User
    {
        return $this->userLuar;
    }

    public function setUserLuar(?User $userLuar): static
    {
        $this->userLuar = $userLuar;

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

    public function getPensiun(): ?bool
    {
        return $this->pensiun;
    }

    public function setPensiun(bool $pensiun): self
    {
        $this->pensiun = $pensiun;

        return $this;
    }

    public function getNpwp(): ?string
    {
        return $this->npwp;
    }

    public function setNpwp(?string $npwp): self
    {
        $this->npwp = $npwp;

        return $this;
    }

    public function getNik(): ?string
    {
        return $this->nik;
    }

    public function setNik(?string $nik): self
    {
        $this->nik = $nik;

        return $this;
    }

    public function getNip18(): ?string
    {
        return $this->nip18;
    }

    public function setNip18(?string $nip18): self
    {
        $this->nip18 = $nip18;

        return $this;
    }

    #[ORM\PrePersist]
    public function setPensiunValue(): void
    {
        $this->pensiun = false;
    }

    /**
     * @return Collection|JabatanPegawaiLuar[]
     */
    public function getJabatanPegawaiLuars(): Collection|array
    {
        return $this->jabatanPegawaiLuars;
    }

    public function addJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): self
    {
        if (!$this->jabatanPegawaiLuars->contains($jabatanPegawaiLuar)) {
            $this->jabatanPegawaiLuars[] = $jabatanPegawaiLuar;
            $jabatanPegawaiLuar->setPegawaiLuar($this);
        }

        return $this;
    }

    public function removeJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): self
    {
        if ($this->jabatanPegawaiLuars->contains($jabatanPegawaiLuar)) {
            $this->jabatanPegawaiLuars->removeElement($jabatanPegawaiLuar);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawaiLuar->getPegawaiLuar() === $this) {
                $jabatanPegawaiLuar->setPegawaiLuar(null);
            }
        }

        return $this;
    }

    public function getPangkat(): ?string
    {
        return $this->pangkat;
    }

    public function setPangkat(?string $pangkat): self
    {
        $this->pangkat = $pangkat;

        return $this;
    }

    /**
     * @return array
     */
    public function getActivePositionIds(): array
    {
        $activePositions = [];
        foreach ($this->getJabatanPegawaiLuars() as $jabatanPegawaiLuar) {
            if ($jabatanPegawaiLuar->getTanggalMulai() <= new DateTimeImmutable('now')
                && ($jabatanPegawaiLuar->getTanggalSelesai() >= new DateTimeImmutable('now')
                    || null === $jabatanPegawaiLuar->getTanggalSelesai())
            ) {
                $activePositions[] = $jabatanPegawaiLuar->getId();
            }
        }

        return $activePositions;
    }
}

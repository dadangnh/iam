<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
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
use App\Doctrine\Filter\NullFilter;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawaiLuar;
use App\Repository\Organisasi\JabatanLuarRepository;
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
 * Jabatan Luar Class
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
            'jabatan-luar:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'jabatan-luar:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    order: [
        'level' => 'ASC',
        'nama' => 'ASC'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: JabatanLuarRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'jabatan_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'level',
        'jenis'
    ],
    name: 'idx_jabatan_luar_nama_status'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode'
    ],
    name: 'idx_jabatan_luar_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'eselon_id'
    ],
    name: 'idx_jabatan_luar_relation'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'tanggal_aktif',
        'tanggal_nonaktif'
    ],
    name: 'idx_jabatan_luar_active'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'jenis' => 'ipartial',
        'legacyKode' => 'partial',
        'eselon.id' => 'exact',
        'eselon.nama' => 'ipartial',
        'eselon.kode' => 'ipartial',
        'eselon.tingkat' => 'exact',
        'units.id' => 'exact',
        'units.nama' => 'ipartial',
        'units.legacyKode' => 'partial',
        'kantor.id' => 'exact',
        'kantor.nama' => 'ipartial',
        'kantor.legacyKode' => 'partial',
        'kantor.legacyKodeKpp' => 'partial',
        'kantor.legacyKodeKanwil' => 'partial'
    ]
)]
#[ApiFilter(
    filterClass: DateFilter::class,
    properties: [
        'tanggalAktif',
        'tanggalNonaktif'
    ]
)]
#[ApiFilter(
    filterClass: NumericFilter::class,
    properties: [
        'level'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
#[ApiFilter(
    filterClass: NullFilter::class
)]
class JabatanLuar
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
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
            'jabatan-luar:read',
            'jabatan-luar:write',
            'user:read',
            'pegawai:read'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: Types::INTEGER
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write',
            'pegawai:read'
        ]
    )]
    private ?int $level;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write',
            'pegawai:read'
        ]
    )]
    private ?string $jenis;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalAktif;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalNonaktif;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
        ]
    )]
    private ?string $sk;

    #[ORM\ManyToOne(
        targetEntity: Eselon::class,
        inversedBy: 'jabatans'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
        ]
    )]
    private ?Eselon $eselon;

    #[ORM\Column(
        type: Types::STRING,
        length: 4,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
        ]
    )]
    private ?string $legacyKode;

    #[ORM\ManyToOne(inversedBy: 'jabatanLuars')]
    #[Groups(
        groups: [
            'jabatan-luar:read',
            'jabatan-luar:write'
        ]
    )]
    private ?GroupJabatanLuar $GroupJabatanLuar;

    #[ORM\ManyToMany(
        targetEntity: UnitLuar::class,
        mappedBy: 'jabatanLuar'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'jabatan-luar:write'
        ]
    )]
    private Collection $unitLuars;

    #[ORM\OneToMany(
        mappedBy: 'jabatanLuar',
        targetEntity: JabatanPegawaiLuar::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'jabatan-luar:write'
        ]
    )]
    private Collection $jabatanPegawaiLuars;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'jabatanLuars')]
    private Collection $roles;


    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->unitLuars = new ArrayCollection();
        $this->jabatanPegawaiLuars = new ArrayCollection();
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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getJenis(): ?string
    {
        return $this->jenis;
    }

    public function setJenis(string $jenis): self
    {
        $this->jenis = $jenis;

        return $this;
    }

    public function getTanggalAktif(): ?DateTimeImmutable
    {
        return $this->tanggalAktif;
    }

    public function setTanggalAktif(DateTimeImmutable $tanggalAktif): self
    {
        $this->tanggalAktif = $tanggalAktif;

        return $this;
    }

    #[ORM\PrePersist]
    public function setTanggalAktifValue(): void
    {
        // Only create tanggal Aktif if no date provided
        if (!isset($this->tanggalAktif)) {
            $this->tanggalAktif = new DateTimeImmutable();
        }
    }

    public function getTanggalNonaktif(): ?DateTimeImmutable
    {
        return $this->tanggalNonaktif;
    }

    public function setTanggalNonaktif(?DateTimeImmutable $tanggalNonaktif): self
    {
        $this->tanggalNonaktif = $tanggalNonaktif;

        return $this;
    }

    public function getSk(): ?string
    {
        return $this->sk;
    }

    public function setSk(?string $sk): self
    {
        $this->sk = $sk;

        return $this;
    }

    public function getEselon(): ?Eselon
    {
        return $this->eselon;
    }

    public function setEselon(?Eselon $eselon): self
    {
        $this->eselon = $eselon;

        return $this;
    }

    public function getLegacyKode(): ?string
    {
        return $this->legacyKode;
    }

    public function setLegacyKode(?string $legacyKode): self
    {
        $this->legacyKode = $legacyKode;

        return $this;
    }

    public function getGroupJabatanLuar(): ?GroupJabatanLuar
    {
        return $this->GroupJabatanLuar;
    }

    public function setGroupJabatanLuar(?GroupJabatanLuar $GroupJabatanLuar): self
    {
        $this->GroupJabatanLuar = $GroupJabatanLuar;

        return $this;
    }

    /**
     * @return Collection<int, UnitLuar>
     */
    public function getUnitLuars(): Collection
    {
        return $this->unitLuars;
    }

    public function addUnitLuar(UnitLuar $unitLuar): static
    {
        if (!$this->unitLuars->contains($unitLuar)) {
            $this->unitLuars->add($unitLuar);
            $unitLuar->addJabatanLuar($this);
        }

        return $this;
    }

    public function removeUnitLuar(UnitLuar $unitLuar): static
    {
        if ($this->unitLuars->removeElement($unitLuar)) {
            $unitLuar->removeJabatanLuar($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, JabatanPegawaiLuar>
     */
    public function getJabatanPegawaiLuars(): Collection
    {
        return $this->jabatanPegawaiLuars;
    }

    public function addJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): static
    {
        if (!$this->jabatanPegawaiLuars->contains($jabatanPegawaiLuar)) {
            $this->jabatanPegawaiLuars->add($jabatanPegawaiLuar);
            $jabatanPegawaiLuar->setJabatanLuar($this);
        }

        return $this;
    }

    public function removeJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): static
    {
        if ($this->jabatanPegawaiLuars->removeElement($jabatanPegawaiLuar)) {
            // set the owning side to null (unless already changed)
            if ($jabatanPegawaiLuar->getJabatanLuar() === $this) {
                $jabatanPegawaiLuar->setJabatanLuar(null);
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

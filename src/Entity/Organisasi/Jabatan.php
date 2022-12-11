<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\JabatanRepository;
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
 * Jabatan Class
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
            'jabatan:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'jabatan:write'
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
    repositoryClass: JabatanRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'jabatan'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'level',
        'jenis'
    ],
    name: 'idx_jabatan_nama_status'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode'
    ],
    name: 'idx_jabatan_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'eselon_id'
    ],
    name: 'idx_jabatan_relation'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'tanggal_aktif',
        'tanggal_nonaktif'
    ],
    name: 'idx_jabatan_active'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'jenis' => 'ipartial',
        'legacyKode' => 'partial',
        'legacyKodeJabKeu' => 'partial',
        'legacyKodeGradeKeu' => 'partial',
        'eselon.id' => 'exact',
        'eselon.nama' => 'ipartial',
        'eselon.kode' => 'ipartial',
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
class Jabatan
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'jabatan:read',
            'jabatan:write'
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
            'jabatan:read',
            'jabatan:write',
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
            'jabatan:read',
            'jabatan:write',
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
            'jabatan:read',
            'jabatan:write',
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
            'jabatan:read',
            'jabatan:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalAktif;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan:read',
            'jabatan:write'
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
            'jabatan:read',
            'jabatan:write'
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
            'jabatan:read',
            'jabatan:write'
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
            'jabatan:read',
            'jabatan:write'
        ]
    )]
    private ?string $legacyKode;

    #[ORM\Column(
        type: Types::STRING,
        length: 4,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan:read',
            'jabatan:write'
        ]
    )]
    private ?string $legacyKodeJabKeu;

    #[ORM\Column(
        type: Types::STRING,
        length: 4,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan:read',
            'jabatan:write'
        ]
    )]
    private ?string $legacyKodeGradeKeu;

    #[ORM\OneToMany(
        mappedBy: 'jabatan',
        targetEntity: JabatanPegawai::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'jabatan:write'
        ]
    )]
    private Collection $jabatanPegawais;

    #[ORM\ManyToMany(
        targetEntity: Unit::class,
        inversedBy: 'jabatans'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'jabatan:write'
        ]
    )]
    private Collection $units;

    #[ORM\ManyToMany(
        targetEntity: Role::class,
        mappedBy: 'jabatans'
    )]
    #[Groups(
        groups: [
            'jabatan:write'
        ]
    )]
    private Collection $roles;

    #[ORM\ManyToOne(
        targetEntity: GroupJabatan::class,
        inversedBy: 'jabatans'
    )]
    #[Groups(
        groups: [
            'jabatan:read',
            'jabatan:write'
        ]
    )]
    private ?GroupJabatan $groupJabatan;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->jabatanPegawais = new ArrayCollection();
        $this->units = new ArrayCollection();
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

    public function getLegacyKodeJabKeu(): ?string
    {
        return $this->legacyKodeJabKeu;
    }

    public function setLegacyKodeJabKeu(?string $legacyKodeJabKeu): self
    {
        $this->legacyKodeJabKeu = $legacyKodeJabKeu;

        return $this;
    }

    public function getLegacyKodeGradeKeu(): ?string
    {
        return $this->legacyKodeGradeKeu;
    }

    public function setLegacyKodeGradeKeu(?string $legacyKodeGradeKeu): self
    {
        $this->legacyKodeGradeKeu = $legacyKodeGradeKeu;

        return $this;
    }

    /**
     * @return Collection|JabatanPegawai[]
     */
    public function getJabatanPegawais(): Collection|array
    {
        return $this->jabatanPegawais;
    }

    public function addJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if (!$this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais[] = $jabatanPegawai;
            $jabatanPegawai->setJabatan($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getJabatan() === $this) {
                $jabatanPegawai->setJabatan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Unit[]
     */
    public function getUnits(): Collection|array
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units[] = $unit;
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
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

    public function getGroupJabatan(): ?GroupJabatan
    {
        return $this->groupJabatan;
    }

    public function setGroupJabatan(?GroupJabatan $groupJabatan): self
    {
        $this->groupJabatan = $groupJabatan;

        return $this;
    }
}

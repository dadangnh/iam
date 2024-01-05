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
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawaiLuar;
use App\Repository\Organisasi\UnitLuarRepository;
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
 * Unit Class
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
            'unit-luar:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'unit-luar:write'
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
    repositoryClass: UnitLuarRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'unit_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'level'
    ],
    name: 'idx_unit_luar_nama'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode',
        'pembina_id'
    ],
    name: 'idx_unit_luar_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'jenis_kantor_luar_id',
        'parent_id',
        'eselon_id'
    ],
    name: 'idx_unit_luar_relation'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'tanggal_aktif',
        'tanggal_nonaktif'
    ],
    name: 'idx_unit_luar_active'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'legacyKode' => 'partial',
        'parent.id' => 'exact',
        'parent.nama' => 'iexact',
        'parent.legacyKode' => 'exact',
        'childs.id' => 'exact',
        'childs.nama' => 'iexact',
        'childs.legacyKode' => 'exact',
        'pembina.id' => 'exact',
        'pembina.nama' => 'iexact',
        'pembina.legacyKode' => 'exact',
        'membina.id' => 'exact',
        'membina.nama' => 'iexact',
        'membina.legacyKode' => 'exact',
        'jenisKantor.id' => 'exact',
        'jenisKantor.nama' => 'iexact',
        'jenisKantor.tipe' => 'iexact',
        'eselon.id' => 'exact',
        'eselon.nama' => 'ipartial',
        'eselon.kode' => 'ipartial'
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
class UnitLuar
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
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
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?string $nama;

    #[ORM\ManyToOne(
        targetEntity: JenisKantorLuar::class,
        inversedBy: 'unitLuars'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?JenisKantorLuar $jenisKantorLuar;

    #[ORM\Column(
        type: Types::INTEGER
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?int $level;

    #[ORM\ManyToOne(
        targetEntity: UnitLuar::class,
        inversedBy: 'childs'
    )]
    #[Assert\Valid]
    private ?UnitLuar $parent;

    #[ORM\OneToMany(
        mappedBy: 'parent',
        targetEntity: UnitLuar::class
    )]
    private Collection $childs;

    #[ORM\ManyToOne(
        targetEntity: Eselon::class,
        inversedBy: 'units'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?Eselon $eselon;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalAktif;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalNonaktif;

    #[ORM\Column(
        type: Types::STRING,
        length: 10,
        nullable: true
    )]
    #[Groups(
        groups: [
            'unit-luar:read',
            'unit-luar:write'
        ]
    )]
    private ?string $legacyKode;
    #[ORM\OneToMany(
        mappedBy: 'unitLuar',
        targetEntity: JabatanPegawaiLuar::class
    )]
    private Collection $jabatanPegawaiLuars;

    #[ORM\ManyToMany(
        targetEntity: JabatanLuar::class,
        inversedBy: 'unitLuars'
    )]
    private Collection $jabatanLuar;

    #[ORM\ManyToOne(
        targetEntity: UnitLuar::class,
        inversedBy: 'membina'
    )]
    private ?UnitLuar $pembina;

    #[ORM\OneToMany(
        mappedBy: 'pembina',
        targetEntity: UnitLuar::class
    )]
    private Collection $membina;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'unitLuars')]
    private Collection $roles;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->childs = new ArrayCollection();
        $this->jabatanPegawaiLuars = new ArrayCollection();
        $this->jabatanLuar = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->membina = new ArrayCollection();
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

    public function getJenisKantorLuar(): ?JenisKantorLuar
    {
        return $this->jenisKantorLuar;
    }

    public function setJenisKantorLuar(?JenisKantorLuar $jenisKantorLuar): self
    {
        $this->jenisKantorLuar = $jenisKantorLuar;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChilds(): Collection|array
    {
        return $this->childs;
    }

    public function addChild(self $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->childs->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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

    public function getLegacyKode(): ?string
    {
        return $this->legacyKode;
    }

    public function setLegacyKode(?string $legacyKode): self
    {
        $this->legacyKode = $legacyKode;

        return $this;
    }

    /**
     * @return Collection|JabatanPegawaiLuar[]
     */
    public function getJabatanPegawaiLuars(): Collection|array
    {
        return $this->jabatanPegawaiLuars;
    }

    public function addJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): static
    {
        if (!$this->jabatanPegawaiLuars->contains($jabatanPegawaiLuar)) {
            $this->jabatanPegawaiLuars[] = $jabatanPegawaiLuar;
            $jabatanPegawaiLuar->setUnitLuar($this);
        }

        return $this;
    }

    public function removeJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): static
    {
        if ($this->jabatanPegawaiLuars->contains($jabatanPegawaiLuar)) {
            $this->jabatanPegawaiLuars->removeElement($jabatanPegawaiLuar);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawaiLuar->getUnitLuar() === $this) {
                $jabatanPegawaiLuar->setUnitLuar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JabatanLuar[]
     */
    public function getJabatanLuar(): Collection|array
    {
        return $this->jabatanLuar;
    }

    public function addJabatanLuar(JabatanLuar $jabatanLuar): self
    {
        if (!$this->jabatanLuar->contains($jabatanLuar)) {
            $this->jabatanLuar[] = $jabatanLuar;
            $jabatanLuar->addUnitLuar($this);
        }

        return $this;
    }

    public function removeJabatanLuar(JabatanLuar $jabatanLuar): static
    {
        if ($this->jabatanLuar->contains($jabatanLuar)) {
            $this->jabatanLuar->removeElement($jabatanLuar);
            $jabatanLuar->removeUnitLuar($this);
        }

        return $this;
    }

    public function getPembina(): ?self
    {
        return $this->pembina;
    }

    public function setPembina(?self $pembina): self
    {
        $this->pembina = $pembina;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getMembina(): Collection|array
    {
        return $this->membina;
    }

    public function addMembina(self $membina): self
    {
        if (!$this->membina->contains($membina)) {
            $this->membina[] = $membina;
            $membina->setPembina($this);
        }

        return $this;
    }

    public function removeMembina(self $membina): self
    {
        if ($this->membina->removeElement($membina)) {
            // set the owning side to null (unless already changed)
            if ($membina->getPembina() === $this) {
                $membina->setPembina(null);
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

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
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\UnitRepository;
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
            'unit:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'unit:write'
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
    repositoryClass: UnitRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'unit'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'level'
    ],
    name: 'idx_unit_nama'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode',
        'pembina_id'
    ],
    name: 'idx_unit_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'jenis_kantor_id',
        'parent_id',
        'eselon_id'
    ],
    name: 'idx_unit_relation'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'tanggal_aktif',
        'tanggal_nonaktif'
    ],
    name: 'idx_unit_active'
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
class Unit
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'unit:read',
            'unit:write'
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
            'pegawai:read',
            'unit:read',
            'unit:write'
        ]
    )]
    private ?string $nama;

    #[ORM\ManyToOne(
        targetEntity: JenisKantor::class,
        inversedBy: 'units'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'unit:read',
            'unit:write'
        ]
    )]
    private ?JenisKantor $jenisKantor;

    #[ORM\Column(
        type: Types::INTEGER
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'pegawai:read',
            'unit:read',
            'unit:write'
        ]
    )]
    private ?int $level;

    #[ORM\ManyToOne(
        targetEntity: Unit::class,
        inversedBy: 'childs'
    )]
    #[Assert\Valid]
    private ?Unit $parent;

    #[ORM\OneToMany(
        mappedBy: 'parent',
        targetEntity: Unit::class
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
            'unit:read',
            'unit:write'
        ]
    )]
    private ?Eselon $eselon;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'unit:read',
            'unit:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalAktif;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'unit:read',
            'unit:write'
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
            'unit:read',
            'unit:write'
        ]
    )]
    private ?string $legacyKode;

    #[ORM\OneToMany(
        mappedBy: 'unit',
        targetEntity: JabatanPegawai::class
    )]
    private Collection $jabatanPegawais;

    #[ORM\ManyToMany(
        targetEntity: Jabatan::class,
        mappedBy: 'units'
    )]
    private Collection $jabatans;

    #[ORM\ManyToMany(
        targetEntity: Role::class,
        mappedBy: 'units'
    )]
    private Collection $roles;

    #[ORM\ManyToOne(
        targetEntity: Unit::class,
        inversedBy: 'membina'
    )]
    private ?Unit $pembina;

    #[ORM\OneToMany(
        mappedBy: 'pembina',
        targetEntity: Unit::class
    )]
    private Collection $membina;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->childs = new ArrayCollection();
        $this->jabatanPegawais = new ArrayCollection();
        $this->jabatans = new ArrayCollection();
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

    public function getJenisKantor(): ?JenisKantor
    {
        return $this->jenisKantor;
    }

    public function setJenisKantor(?JenisKantor $jenisKantor): self
    {
        $this->jenisKantor = $jenisKantor;

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
            $jabatanPegawai->setUnit($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getUnit() === $this) {
                $jabatanPegawai->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Jabatan[]
     */
    public function getJabatans(): Collection|array
    {
        return $this->jabatans;
    }

    public function addJabatan(Jabatan $jabatan): self
    {
        if (!$this->jabatans->contains($jabatan)) {
            $this->jabatans[] = $jabatan;
            $jabatan->addUnit($this);
        }

        return $this;
    }

    public function removeJabatan(Jabatan $jabatan): self
    {
        if ($this->jabatans->contains($jabatan)) {
            $this->jabatans->removeElement($jabatan);
            $jabatan->removeUnit($this);
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
}

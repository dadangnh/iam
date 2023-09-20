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
use App\Repository\Organisasi\JenisKantorLuarRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * JenisKantor Class
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
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: JenisKantorLuarRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'jenis_kantor_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'tipe',
        'klasifikasi'
    ],
    name: 'idx_jenis_kantor_luar_nama_status'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode',
        'legacy_id'
    ],
    name: 'idx_jenis_kantor_luar_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'tanggal_aktif',
        'tanggal_nonaktif'
    ],
    name: 'idx_jenis_kantor_luar_active'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'tipe' => 'ipartial'
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
        'klasifikasi',
        'legacyId',
        'legacyKode'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class JenisKantorLuar
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
    #[Assert\NotNull]
    private ?string $tipe;

    #[ORM\Column(
        type: Types::INTEGER
    )]
    #[Assert\NotNull]
    private ?int $klasifikasi;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    #[Assert\NotNull]
    private ?DateTimeImmutable $tanggalAktif;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    private ?DateTimeImmutable $tanggalNonaktif;

    #[ORM\Column(
        type: Types::INTEGER,
        nullable: true
    )]
    private ?int $legacyId;

    #[ORM\Column(
        type: Types::INTEGER,
        nullable: true
    )]
    private ?int $legacyKode;

    #[ORM\OneToMany(
        mappedBy: 'JenisKantorLuar',
        targetEntity: UnitLuar::class
    )]
    private Collection $unitLuars;

    #[ORM\OneToMany(
        mappedBy: 'jenisKantorLuar',
        targetEntity: KantorLuar::class
    )]
    private Collection $kantorLuars;

    #[ORM\ManyToMany(
        targetEntity: Role::class,
        mappedBy: 'jenisKantors'
    )]
    private Collection $roles;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->unitLuars = new ArrayCollection();
        $this->kantorLuars = new ArrayCollection();
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

    public function getTipe(): ?string
    {
        return $this->tipe;
    }

    public function setTipe(string $tipe): self
    {
        $this->tipe = $tipe;

        return $this;
    }

    public function getKlasifikasi(): ?int
    {
        return $this->klasifikasi;
    }

    public function setKlasifikasi(int $klasifikasi): self
    {
        $this->klasifikasi = $klasifikasi;

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

    public function getLegacyId(): ?int
    {
        return $this->legacyId;
    }

    public function setLegacyId(?int $legacyId): self
    {
        $this->legacyId = $legacyId;

        return $this;
    }

    public function getLegacyKode(): ?int
    {
        return $this->legacyKode;
    }

    public function setLegacyKode(?int $legacyKode): self
    {
        $this->legacyKode = $legacyKode;

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
            $unitLuar->setJenisKantorLuar($this);
        }

        return $this;
    }

    public function removeUnitLuar(UnitLuar $unitLuar): static
    {
        if ($this->unitLuars->removeElement($unitLuar)) {
            // set the owning side to null (unless already changed)
            if ($unitLuar->getJenisKantorLuar() === $this) {
                $unitLuar->setJenisKantorLuar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, KantorLuar>
     */
    public function getKantorLuars(): Collection
    {
        return $this->kantorLuars;
    }

    public function addKantorLuar(KantorLuar $kantorLuar): static
    {
        if (!$this->kantorLuars->contains($kantorLuar)) {
            $this->kantorLuars->add($kantorLuar);
            $kantorLuar->setJenisKantorLuar($this);
        }

        return $this;
    }

    public function removeKantorLuar(KantorLuar $kantorLuar): static
    {
        if ($this->kantorLuars->removeElement($kantorLuar)) {
            // set the owning side to null (unless already changed)
            if ($kantorLuar->getJenisKantorLuar() === $this) {
                $kantorLuar->setJenisKantorLuar(null);
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

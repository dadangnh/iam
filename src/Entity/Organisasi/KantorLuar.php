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
use App\Repository\Organisasi\KantorLuarRepository;
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
 * Kantor Class
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
            'kantor-luar:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'kantor-luar:write'
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
    repositoryClass: KantorLuarRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'kantor_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'level',
        'sk'
    ],
    name: 'idx_kantor_luar_nama_status'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode',
        'legacy_kode_kpp',
        'legacy_kode_kanwil',
        'ministry_office_code'
    ],
    name: 'idx_kantor_luar_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'jenis_kantor_luar_id',
        'parent_id',
        'level',
        'pembina_id'
    ],
    name: 'idx_kantor_luar_relation'
)]
#[ORM\Index(
    columns: [
        'id',
        'latitude',
        'longitude'
    ],
    name: 'idx_kantor_luar_location'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'tanggal_aktif',
        'tanggal_nonaktif'
    ],
    name: 'idx_kantor_luar_active'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'legacy_kode',
        'provinsi',
        'provinsi_name',
        'kabupaten_kota',
        'kabupaten_kota_name',
        'kecamatan',
        'kecamatan_name',
        'kelurahan',
        'kelurahan_name'
    ],
    name: 'idx_kantor_luar_position'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'sk' => 'ipartial',
        'legacyKode' => 'partial',
        'legacyKodeKpp' => 'partial',
        'legacyKodeKanwil' => 'partial',
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
        'alamat' => 'ipartial',
        'telp' => 'exact',
        'fax' => 'exact',
        'zonaWaktu' => 'exact',
        'jenisKantor.id' => 'exact',
        'jenisKantor.nama' => 'iexact',
        'jenisKantor.tipe' => 'iexact',
        'provinsi' => 'iexact',
        'provinsiName' => 'iexact',
        'kabupatenKota' => 'iexact',
        'kabupatenKotaName' => 'iexact',
        'kecamatan' => 'iexact',
        'kecamatanName' => 'iexact',
        'kelurahan' => 'iexact',
        'kelurahanName' => 'iexact',
        'ministryOfficeCode' => 'exact'
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
        'level',
        'jenisKantor.klasifikasi',
        'jenisKantor.legacyId',
        'jenisKantor.legacyKode'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
#[ApiFilter(
    filterClass: NullFilter::class
)]
class KantorLuar
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
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
            'user:read',
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $nama;
    #[ORM\ManyToOne(
        targetEntity: JenisKantorLuar::class,
        inversedBy: 'kantorLuars'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?JenisKantorLuar $jenisKantorLuar;


    #[ORM\Column(
        type: Types::INTEGER,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?int $level;

    #[ORM\ManyToOne(
        targetEntity: KantorLuar::class,
        inversedBy: 'childs'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?KantorLuar $parent;

    #[ORM\OneToMany(
        mappedBy: 'parent',
        targetEntity: KantorLuar::class
    )]
    private Collection $childs;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalAktif;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
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
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $sk;

    #[ORM\Column(
        type: Types::TEXT,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $alamat;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $telp;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $fax;

    #[ORM\Column(
        type: Types::STRING,
        length: 4,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $zonaWaktu;

    #[ORM\Column(
        type: Types::FLOAT,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?float $latitude;

    #[ORM\Column(
        type: Types::FLOAT,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?float $longitude;

    #[ORM\Column(
        type: Types::STRING,
        length: 10,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $legacyKode;

    #[ORM\Column(
        type: Types::STRING,
        length: 3,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $legacyKodeKpp;

    #[ORM\Column(
        type: Types::STRING,
        length: 3,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $legacyKodeKanwil;

    #[ORM\OneToMany(
        mappedBy: 'kantorLuar',
        targetEntity: JabatanPegawaiLuar::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:write'
        ]
    )]
    private Collection $jabatanPegawaiLuars;

    #[ORM\ManyToOne(
        targetEntity: KantorLuar::class,
        inversedBy: 'membina'
    )]
    #[Groups(
        groups: [
            'kantor-luar:write'
        ]
    )]
    private ?KantorLuar $pembina;

    #[ORM\OneToMany(
        mappedBy: 'pembina',
        targetEntity: KantorLuar::class
    )]
    #[Groups(
        groups: [
            'kantor-luar:write'
        ]
    )]
    private Collection $membina;

    #[ORM\Column(
        type: 'uuid',
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?UuidV4 $provinsi;

    #[ORM\Column(
        type: 'uuid',
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?UuidV4 $kabupatenKota;

    #[ORM\Column(
        type: 'uuid',
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?UuidV4 $kecamatan;

    #[ORM\Column(
        type: 'uuid',
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?UuidV4 $kelurahan;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $provinsiName = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $kabupatenKotaName = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $kecamatanName = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $kelurahanName = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 10,
        nullable: true
    )]
    #[Groups(
        groups: [
            'kantor-luar:read',
            'kantor-luar:write'
        ]
    )]
    private ?string $ministryOfficeCode = null;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'kantorLuars')]
    private Collection $roles;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->childs = new ArrayCollection();
        $this->jabatanPegawaiLuars = new ArrayCollection();
        $this->membina = new ArrayCollection();
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

    public function setLevel(?int $level): self
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

    public function getAlamat(): ?string
    {
        return $this->alamat;
    }

    public function setAlamat(?string $alamat): self
    {
        $this->alamat = $alamat;

        return $this;
    }

    public function getTelp(): ?string
    {
        return $this->telp;
    }

    public function setTelp(?string $telp): self
    {
        $this->telp = $telp;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getZonaWaktu(): ?string
    {
        return $this->zonaWaktu;
    }

    public function setZonaWaktu(?string $zonaWaktu): self
    {
        $this->zonaWaktu = $zonaWaktu;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

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

    public function getLegacyKodeKpp(): ?string
    {
        return $this->legacyKodeKpp;
    }

    public function setLegacyKodeKpp(?string $legacyKodeKpp): self
    {
        $this->legacyKodeKpp = $legacyKodeKpp;

        return $this;
    }

    public function getLegacyKodeKanwil(): ?string
    {
        return $this->legacyKodeKanwil;
    }

    public function setLegacyKodeKanwil(?string $legacyKodeKanwil): self
    {
        $this->legacyKodeKanwil = $legacyKodeKanwil;

        return $this;
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
            $jabatanPegawaiLuar->setKantorLuar($this);
        }

        return $this;
    }

    public function removeJabatanPegawaiLuar(JabatanPegawaiLuar $jabatanPegawaiLuar): static
    {
        if ($this->jabatanPegawaiLuars->contains($jabatanPegawaiLuar)) {
            $this->jabatanPegawaiLuars->removeElement($jabatanPegawaiLuar);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawaiLuar->getKantor() === $this) {
                $jabatanPegawaiLuar->setKantor(null);
            }
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

    public function getProvinsi(): ?UuidV4
    {
        return $this->provinsi;
    }

    public function setProvinsi($provinsi): self
    {
        if (Uuid::isValid($provinsi) && UuidV4::isValid($provinsi)) {
            if (is_string($provinsi)) {
                $provinsi = UuidV4::fromString($provinsi);
            }
            $this->provinsi = $provinsi;
        }

        return $this;
    }

    public function getKabupatenKota(): ?UuidV4
    {
        return $this->kabupatenKota;
    }

    public function setKabupatenKota($kabupatenKota): self
    {
        if (Uuid::isValid($kabupatenKota) && UuidV4::isValid($kabupatenKota)) {
            if (is_string($kabupatenKota)) {
                $kabupatenKota = UuidV4::fromString($kabupatenKota);
            }
            $this->kabupatenKota = $kabupatenKota;
        }

        return $this;
    }

    public function getKecamatan(): ?UuidV4
    {
        return $this->kecamatan;
    }

    public function setKecamatan($kecamatan): self
    {
        if (Uuid::isValid($kecamatan) && UuidV4::isValid($kecamatan)) {
            if (is_string($kecamatan)) {
                $kecamatan = UuidV4::fromString($kecamatan);
            }
            $this->kecamatan = $kecamatan;
        }

        return $this;
    }

    public function getKelurahan(): ?UuidV4
    {
        return $this->kelurahan;
    }

    public function setKelurahan($kelurahan): self
    {
        if (Uuid::isValid($kelurahan) && UuidV4::isValid($kelurahan)) {
            if (is_string($kelurahan)) {
                $kelurahan = UuidV4::fromString($kelurahan);
            }
            $this->kelurahan = $kelurahan;
        }

        return $this;
    }

    public function getProvinsiName(): ?string
    {
        return $this->provinsiName;
    }

    public function setProvinsiName(?string $provinsiName): self
    {
        $this->provinsiName = $provinsiName;

        return $this;
    }

    public function getKabupatenKotaName(): ?string
    {
        return $this->kabupatenKotaName;
    }

    public function setKabupatenKotaName(?string $kabupatenKotaName): self
    {
        $this->kabupatenKotaName = $kabupatenKotaName;

        return $this;
    }

    public function getKecamatanName(): ?string
    {
        return $this->kecamatanName;
    }

    public function setKecamatanName(?string $kecamatanName): self
    {
        $this->kecamatanName = $kecamatanName;

        return $this;
    }

    public function getKelurahanName(): ?string
    {
        return $this->kelurahanName;
    }

    public function setKelurahanName(?string $kelurahanName): self
    {
        $this->kelurahanName = $kelurahanName;

        return $this;
    }

    public function getMinistryOfficeCode(): ?string
    {
        return $this->ministryOfficeCode;
    }

    public function setMinistryOfficeCode(?string $ministryOfficeCode): self
    {
        $this->ministryOfficeCode = $ministryOfficeCode;

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

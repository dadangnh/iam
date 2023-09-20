<?php

namespace App\Entity\Pegawai;

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
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanAtribut;
use App\Entity\Organisasi\JabatanLuar;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\KantorLuar;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Entity\Organisasi\UnitLuar;
use App\Repository\Pegawai\JabatanPegawaiLuarRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * JabatanPegawai Class
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
    repositoryClass: JabatanPegawaiLuarRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'jabatan_pegawai_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'tanggal_mulai',
        'tanggal_selesai'
    ],
    name: 'idx_jabatan_pegawai_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'pegawai_luar_id',
        'jabatan_luar_id',
        'tipe_id',
        'kantor_luar_id',
        'unit_luar_id'
    ],
    name: 'idx_jabatan_pegawai_luar_relation'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'referensi' => 'ipartial',
        'pegawai.id' => 'exact',
        'pegawai.nama' => 'ipartial',
        'pegawai.nip9' => 'partial',
        'pegawai.nip18' => 'partial',
        'pegawai.user.username' => 'ipartial',
        'jabatan.id' => 'exact',
        'jabatan.nama' => 'ipartial',
        'jabatan.jenis' => 'ipartial',
        'jabatan.legacyKode' => 'partial',
        'jabatan.legacyKodeJabKeu' => 'partial',
        'jabatan.legacyKodeGradeKeu' => 'partial',
        'eselon.id' => 'exact',
        'eselon.nama' => 'ipartial',
        'eselon.kode' => 'ipartial',
        'tipe.id' => 'exact',
        'tipe.nama' => 'ipartial',
        'unit.id' => 'exact',
        'unit.nama' => 'ipartial',
        'unit.legacyKode' => 'partial',
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
        'tanggalMulai',
        'tanggalSelesai'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class JabatanPegawaiLuar
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    private UuidV4 $id;

    #[ORM\ManyToOne(
        targetEntity: PegawaiLuar::class,
        inversedBy: 'jabatanPegawaiLuars'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    private ?PegawaiLuar $pegawaiLuar;

    #[ORM\ManyToOne(
        targetEntity: JabatanLuar::class,
        inversedBy: 'jabatanPegawaiLuars'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?JabatanLuar $jabatanLuar;

    #[ORM\ManyToOne(
        targetEntity: TipeJabatan::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?TipeJabatan $tipe;

    #[ORM\ManyToOne(
        targetEntity: KantorLuar::class,
        inversedBy: 'jabatanPegawaiLuars'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?KantorLuar $kantorLuar;

    #[ORM\ManyToOne(
        targetEntity: UnitLuar::class,
        inversedBy: 'jabatanPegawaiLuars'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?UnitLuar $unitLuar;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?string $referensi;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?DateTimeImmutable $tanggalMulai;

    #[ORM\Column(
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?DateTimeImmutable $tanggalSelesai;

    #[ORM\ManyToOne(
        targetEntity: JabatanAtribut::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[Groups(
        groups: [
            'pegawai-luar:read'
        ]
    )]
    private ?JabatanAtribut $atribut;

    public function __construct()
    {
        // if id is not set by client, create the id here
        if (empty($this->id)) {
            $this->id = Uuid::v4();
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
    public function getPegawaiLuar(): ?PegawaiLuar
    {
        return $this->pegawaiLuar;
    }

    public function setPegawaiLuar(?PegawaiLuar $pegawaiLuar): self
    {
        $this->pegawaiLuar = $pegawaiLuar;

        return $this;
    }

    public function getJabatanLuar(): ?JabatanLuar
    {
        return $this->jabatanLuar;
    }

    public function setJabatanLuar(?JabatanLuar $jabatanLuar): self
    {
        $this->jabatanLuar = $jabatanLuar;

        return $this;
    }

    public function getTipe(): ?TipeJabatan
    {
        return $this->tipe;
    }

    public function setTipe(?TipeJabatan $tipe): self
    {
        $this->tipe = $tipe;

        return $this;
    }

    public function getKantorLuar(): ?KantorLuar
    {
        return $this->kantorLuar;
    }

    public function setKantorLuar(?KantorLuar $kantorLuar): self
    {
        $this->kantorLuar = $kantorLuar;

        return $this;
    }
    public function getUnitLuar(): ?UnitLuar
    {
        return $this->unitLuar;
    }

    public function setUnitLuar(?UnitLuar $unitLuar): self
    {
        $this->unitLuar = $unitLuar;

        return $this;
    }

    public function getReferensi(): ?string
    {
        return $this->referensi;
    }

    public function setReferensi(?string $referensi): self
    {
        $this->referensi = $referensi;

        return $this;
    }

    public function getTanggalMulai(): ?DateTimeImmutable
    {
        return $this->tanggalMulai;
    }

    public function setTanggalMulai(DateTimeImmutable $tanggalMulai): self
    {
        $this->tanggalMulai = $tanggalMulai;

        return $this;
    }

    #[ORM\PrePersist]
    public function setTanggalMulaiValue(): void
    {
        $this->tanggalMulai = new DateTimeImmutable();
    }

    public function getTanggalSelesai(): ?DateTimeImmutable
    {
        return $this->tanggalSelesai;
    }

    public function setTanggalSelesai(?DateTimeImmutable $tanggalSelesai): self
    {
        $this->tanggalSelesai = $tanggalSelesai;

        return $this;
    }

    public function getAtribut(): ?JabatanAtribut
    {
        return $this->atribut;
    }

    public function setAtribut(?JabatanAtribut $atribut): self
    {
        $this->atribut = $atribut;

        return $this;
    }
}

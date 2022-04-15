<?php

namespace App\Entity\Pegawai;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanAtribut;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Repository\Pegawai\JabatanPegawaiRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * JabatanPegawai Class
 */
#[ORM\Entity(
    repositoryClass: JabatanPegawaiRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'jabatan_pegawai'
)]
#[ORM\Index(
    columns: [
        'id',
        'tanggal_mulai',
        'tanggal_selesai'
    ],
    name: 'idx_jabatan_pegawai'
)]
#[ORM\Index(
    columns: [
        'id',
        'pegawai_id',
        'jabatan_id',
        'tipe_id',
        'kantor_id',
        'unit_id'
    ],
    name: 'idx_jabatan_pegawai_relation'
)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_USER")',
            'security_message' => 'Only a valid user can access this.'
        ],
        'post' => [
            'security'=>'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message'=>'Only admin/app can add new resource to this entity type.'
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_USER")',
            'security_message' => 'Only a valid user can access this.'
        ],
        'put' => [
            'security' => 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message' => 'Only admin/app can add new resource to this entity type.'
        ],
        'patch' => [
            'security' => 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message' => 'Only admin/app can add new resource to this entity type.'
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            'security_message' => 'Only admin/app can add new resource to this entity type.'
        ],
    ],
    attributes: [
        'security' => 'is_granted("ROLE_USER")',
        'security_message' => 'Only a valid user can access this.',
    ],
)]
#[ApiFilter(
    SearchFilter::class,
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
        'unit.legacyKode' => 'partial'
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class JabatanPegawai
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    private UuidV4 $id;

    #[ORM\ManyToOne(
        targetEntity: Pegawai::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    private ?Pegawai $pegawai;

    #[ORM\ManyToOne(
        targetEntity: Jabatan::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?Jabatan $jabatan;

    #[ORM\ManyToOne(
        targetEntity: TipeJabatan::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?TipeJabatan $tipe;

    #[ORM\ManyToOne(
        targetEntity: Kantor::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?Kantor $kantor;

    #[ORM\ManyToOne(
        targetEntity: Unit::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?Unit $unit;

    #[ORM\Column(
        type: 'string',
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?string $referensi;

    #[ORM\Column(
        type: 'datetime_immutable'
    )]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?DateTimeImmutable $tanggalMulai;

    #[ORM\Column(
        type: 'datetime_immutable',
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai:read'
        ]
    )]
    private ?DateTimeImmutable $tanggalSelesai;

    #[ORM\ManyToOne(
        targetEntity: JabatanAtribut::class,
        inversedBy: 'jabatanPegawais'
    )]
    #[Groups(
        groups: [
            'pegawai:read'
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

    public function getPegawai(): ?Pegawai
    {
        return $this->pegawai;
    }

    public function setPegawai(?Pegawai $pegawai): self
    {
        $this->pegawai = $pegawai;

        return $this;
    }

    public function getJabatan(): ?Jabatan
    {
        return $this->jabatan;
    }

    public function setJabatan(?Jabatan $jabatan): self
    {
        $this->jabatan = $jabatan;

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

    public function getKantor(): ?Kantor
    {
        return $this->kantor;
    }

    public function setKantor(?Kantor $kantor): self
    {
        $this->kantor = $kantor;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

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

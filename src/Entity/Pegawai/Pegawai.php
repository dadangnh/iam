<?php

namespace App\Entity\Pegawai;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User\User;
use App\Repository\Pegawai\PegawaiRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pegawai Class
 */
#[ORM\Entity(
    repositoryClass: PegawaiRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'pegawai'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'pensiun',
        'nik',
        'nip9',
        'nip18',
        'pangkat'
    ],
    name: 'idx_pegawai_data'
)]
#[ORM\Index(
    columns: [
        'id',
        'nip9'
    ],
    name: 'idx_pegawai_legacy'
)]
#[ORM\Index(
    columns: [
        'id',
        'user_id'
    ],
    name: 'idx_pegawai_relation'
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
    denormalizationContext: [
        'groups' => ['pegawai:write'],
        'swagger_definition_name' => 'write'
    ],
    normalizationContext: [
        'groups' => ['pegawai:read'],
        'swagger_definition_name' => 'read'
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'nip9' => 'partial',
        'nip18' => 'partial',
        'user.username' => 'ipartial',
        'pangkat' => 'ipartial'
    ]
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'tanggalLahir'
    ]
)]
#[ApiFilter(
    BooleanFilter::class,
    properties: [
        'pensiun'
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class Pegawai
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private UuidV4 $id;

    #[ORM\OneToOne(
        inversedBy: 'pegawai',
        targetEntity: User::class,
        cascade: [
            'persist',
            'remove'
        ]
    )]
    #[ORM\JoinColumn(
        nullable: false
    )]
    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?User $user;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: 'datetime_immutable'
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?DateTimeImmutable $tanggalLahir;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $tempatLahir;

    #[ORM\Column(
        type: 'boolean'
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?bool $pensiun;

    #[ORM\Column(
        type: 'string',
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $npwp;

    #[ORM\Column(
        type: 'string',
        length: 16,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $nik;

    #[ORM\Column(
        type: 'string',
        length: 9,
        nullable: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $nip9;

    #[ORM\Column(
        type: 'string',
        length: 18,
        nullable: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $nip18;

    #[ORM\OneToMany(
        mappedBy: 'pegawai',
        targetEntity: JabatanPegawai::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private Collection $jabatanPegawais;

    #[ORM\Column(
        type: 'string',
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?string $pangkat;

    #[ORM\Column(
        type: 'boolean'
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'pegawai:read',
            'pegawai:write'
        ]
    )]
    private ?bool $onLeave;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->jabatanPegawais = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nama;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function getTanggalLahir(): ?DateTimeImmutable
    {
        return $this->tanggalLahir;
    }

    public function setTanggalLahir(DateTimeImmutable $tanggalLahir): self
    {
        $this->tanggalLahir = $tanggalLahir;

        return $this;
    }

    public function getTempatLahir(): ?string
    {
        return $this->tempatLahir;
    }

    public function setTempatLahir(string $tempatLahir): self
    {
        $this->tempatLahir = $tempatLahir;

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

    public function getNip9(): ?string
    {
        return $this->nip9;
    }

    public function setNip9(?string $nip9): self
    {
        $this->nip9 = $nip9;

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
            $jabatanPegawai->setPegawai($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getPegawai() === $this) {
                $jabatanPegawai->setPegawai(null);
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

    public function getOnLeave(): ?bool
    {
        return $this->onLeave;
    }

    public function setOnLeave(bool $onLeave): self
    {
        $this->onLeave = $onLeave;

        return $this;
    }
}

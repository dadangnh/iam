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
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\TipeJabatanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipeJabatan Class
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
            'tipe-jabatan:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'tipe-jabatan:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    order: [
        'nama' => 'ASC'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: TipeJabatanRepository::class
)]
#[ORM\Table(
    name: 'tipe_jabatan'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama'
    ],
    name: 'idx_tipe_jabatan'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class TipeJabatan
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'tipe-jabatan:read',
            'tipe-jabatan:write'
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
            'tipe-jabatan:read',
            'tipe-jabatan:write'
        ]
    )]
    private ?string $nama;

    #[ORM\OneToMany(
        mappedBy: 'tipe',
        targetEntity: JabatanPegawai::class
    )]
    private Collection $jabatanPegawais;

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

    public function getNama(): ?string
    {
        return $this->nama;
    }

    public function setNama(string $nama): self
    {
        $this->nama = $nama;

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
            $jabatanPegawai->setTipe($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getTipe() === $this) {
                $jabatanPegawai->setTipe(null);
            }
        }

        return $this;
    }
}

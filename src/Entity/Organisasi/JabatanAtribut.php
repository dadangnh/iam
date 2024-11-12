<?php

namespace App\Entity\Organisasi;

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
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\JabatanAtributRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Jabatan Atribut Class
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
            'jabatan-atribut:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'jabatan-atribut:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    order: [
        'nama_atribut' => 'ASC'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: JabatanAtributRepository::class
)]
#[ORM\Table(
    name: 'jabatan_atribut'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama_atribut'
    ],
    name: 'idx_jabatan_atribut_nama'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama_atribut' => 'ipartial'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class JabatanAtribut
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'jabatan-atribut:read',
            'jabatan-atribut:write'
        ]
    )]
    private UuidV4 $id;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Groups(
        groups: [
            'jabatan-atribut:read',
            'jabatan-atribut:write'
        ]
    )]
    #[Assert\NotBlank]
    private ?string $namaAtribut = null;


    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: true
    )]
    #[Groups(
        groups: [
            'jabatan-atribut:read',
            'jabatan-atribut:write'
        ]
    )]
    #[Assert\NotBlank]
    private ?string $parentAtributId = null;

    #[ORM\OneToMany(
        mappedBy: 'atribut',
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
        return $this->namaAtribut;
    }

    public function getId(): Uuid
    {
        return $this->id;
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
            $jabatanPegawai->setAtribut($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getAtribut() === $this) {
                $jabatanPegawai->setAtribut(null);
            }
        }

        return $this;
    }

    public function getNamaAtribut(): ?string
    {
        return $this->namaAtribut;
    }

    public function setNamaAtribut(string $namaAtribut): static
    {
        $this->namaAtribut = $namaAtribut;

        return $this;
    }

    public function getParentAtributId(): ?string
    {
        return $this->parentAtributId;
    }

    public function setParentAtributId(?string $parentAtributId): static
    {
        $this->parentAtributId = $parentAtributId;

        return $this;
    }
}

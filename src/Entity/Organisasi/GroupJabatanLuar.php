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
use App\Repository\Organisasi\GroupJabatanLuarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

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
    repositoryClass: GroupJabatanLuarRepository::class
)]
#[ORM\Table(
    name: 'group_jabatan_luar'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama'
    ],
    name: 'idx_group_jabatan_luar_nama'
)]
#[ORM\Index(
    columns: [
        'id',
        'legacy_kode'
    ],
    name: 'idx_group_jabatan_luar_legacy'
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'legacyKode' => 'iexact'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class GroupJabatanLuar
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
    #[Groups(
        groups: [
            'jabatan-luar:read'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: Types::STRING,
        length: 4,
        nullable: true
    )]
    private ?string $legacyKode;

    #[ORM\OneToMany(
        mappedBy: 'GroupJabatanLuar',
        targetEntity: JabatanLuar::class
    )]
    private Collection $jabatanLuars;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->jabatanLuars = new ArrayCollection();
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
     * @return Collection<int, JabatanLuar>
     */
    public function getJabatanLuars(): Collection
    {
        return $this->jabatanLuars;
    }

    public function addJabatanLuar(JabatanLuar $jabatanLuar): static
    {
        if (!$this->jabatanLuars->contains($jabatanLuar)) {
            $this->jabatanLuars->add($jabatanLuar);
            $jabatanLuar->setGroupJabatanLuar($this);
        }

        return $this;
    }

    public function removeJabatanLuar(JabatanLuar $jabatanLuar): static
    {
        if ($this->jabatanLuars->removeElement($jabatanLuar)) {
            // set the owning side to null (unless already changed)
            if ($jabatanLuar->getGroupJabatanLuar() === $this) {
                $jabatanLuar->setGroupJabatanLuar(null);
            }
        }

        return $this;
    }
}

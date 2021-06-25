<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\TipeJabatanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TipeJabatanRepository::class)
 * @ORM\Table(name="tipe_jabatan", indexes={
 *     @ORM\Index(name="idx_tipe_jabatan", columns={"id", "nama"}),
 * })
 * Disable second level cache for further analysis
 * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
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
        'order' => [
            'nama' => 'ASC'
        ]
    ],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'nama' => 'ipartial',
])]
#[ApiFilter(PropertyFilter::class)]
class TipeJabatan
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotBlank()
     * @Groups({"user:read"})
     */
    private ?string $nama;

    /**
     * @ORM\OneToMany(targetEntity=JabatanPegawai::class, mappedBy="tipe")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $jabatanPegawais;

    #[Pure] public function __construct()
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

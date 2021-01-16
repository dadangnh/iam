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
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *          "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *          "security_message"="Only a valid user/admin/app can access this."
 *     },
 *     collectionOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only a valid user/admin/app can access this."
 *          },
 *         "post"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can add new resource to this entity type."
 *          }
 *     },
 *     itemOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only a valid user/admin/app can access this."
 *          },
 *         "put"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can replace this entity type."
 *          },
 *         "patch"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can edit this entity type."
 *          },
 *         "delete"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can delete this entity type."
 *          },
 *     }
 * )
 * @ORM\Entity(repositoryClass=JabatanPegawaiRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="jabatan_pegawai", indexes={
 *     @ORM\Index(name="idx_jabatan_pegawai", columns={"id", "tanggal_mulai", "tanggal_selesai"}),
 *     @ORM\Index(name="idx_jabatan_pegawai_relation", columns={"id", "pegawai_id", "jabatan_id", "tipe_id", "kantor_id", "unit_id"}),
 * })
 * Disable second level cache for further analysis
 * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ApiFilter(SearchFilter::class, properties={"referensi": "ipartial"})
 * @ApiFilter(PropertyFilter::class)
 */
class JabatanPegawai
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Pegawai::class, inversedBy="jabatanPegawais")
     * @ORM\JoinColumn(nullable=false)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     * @Assert\Valid
     */
    private $pegawai;

    /**
     * @ORM\ManyToOne(targetEntity=Jabatan::class, inversedBy="jabatanPegawais")
     * @ORM\JoinColumn(nullable=false)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"pegawai:read"})
     */
    private $jabatan;

    /**
     * @ORM\ManyToOne(targetEntity=TipeJabatan::class, inversedBy="jabatanPegawais")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"pegawai:read"})
     */
    private $tipe;

    /**
     * @ORM\ManyToOne(targetEntity=Kantor::class, inversedBy="jabatanPegawais")
     * @ORM\JoinColumn(nullable=false)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"pegawai:read"})
     */
    private $kantor;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="jabatanPegawais")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"pegawai:read"})
     * @Assert\Valid()
     */
    private $unit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"pegawai:read"})
     */
    private ?string $referensi;

    /**
     * @ORM\Column(type="datetime_immutable")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"pegawai:read"})
     */
    private ?DateTimeImmutable $tanggalMulai;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"pegawai:read"})
     */
    private ?DateTimeImmutable $tanggalSelesai;

    /**
     * @ORM\ManyToOne(targetEntity=JabatanAtribut::class, inversedBy="jabatanPegawais")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"pegawai:read"})
     */
    private $atribut;

    public function getId()
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

    /**
     * @ORM\PrePersist()
     */
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

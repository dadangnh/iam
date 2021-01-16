<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Repository\Organisasi\JenisKantorRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
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
 * @ORM\Entity(repositoryClass=JenisKantorRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="jenis_kantor", indexes={
 *     @ORM\Index(name="idx_jenis_kantor_nama_status", columns={"id", "nama", "tipe", "klasifikasi"}),
 *     @ORM\Index(name="idx_jenis_kantor_legacy", columns={"id", "legacy_kode", "legacy_id"}),
 * })
 * Disable second level cache for further analysis
 * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ApiFilter(SearchFilter::class, properties={"nama": "ipartial", "tipe": "ipartial"})
 * @ApiFilter(NumericFilter::class, properties={"klasifikasi", "legacyId", "legacyKode"})
 * @ApiFilter(DateFilter::class, properties={"tanggalAktif", "tanggalNonaktif"})
 * @ApiFilter(PropertyFilter::class)
 */
class JenisKantor
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
     * @ORM\Column(type="string", length=255)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotBlank()
     */
    private ?string $nama;

    /**
     * @ORM\Column(type="string", length=255)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     */
    private ?string $tipe;

    /**
     * @ORM\Column(type="integer")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     */
    private ?int $klasifikasi;

    /**
     * @ORM\Column(type="datetime_immutable")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\NotNull()
     */
    private ?DateTimeImmutable $tanggalAktif;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private ?DateTimeImmutable $tanggalNonaktif;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private ?int $legacyId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private ?int $legacyKode;

    /**
     * @ORM\OneToMany(targetEntity=Kantor::class, mappedBy="jenisKantor")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $kantors;

    /**
     * @ORM\OneToMany(targetEntity=Unit::class, mappedBy="jenisKantor")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $units;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="jenisKantors")
     */
    private $roles;

    #[Pure] public function __construct()
    {
        $this->kantors = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nama;
    }

    public function getId()
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

    /**
     * @ORM\PrePersist()
     */
    public function setTanggalAktifValue(): void
    {
        $this->tanggalAktif = new DateTimeImmutable();
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
     * @return Collection|Kantor[]
     */
    public function getKantors(): Collection|array
    {
        return $this->kantors;
    }

    public function addKantor(Kantor $kantor): self
    {
        if (!$this->kantors->contains($kantor)) {
            $this->kantors[] = $kantor;
            $kantor->setJenisKantor($this);
        }

        return $this;
    }

    public function removeKantor(Kantor $kantor): self
    {
        if ($this->kantors->contains($kantor)) {
            $this->kantors->removeElement($kantor);
            // set the owning side to null (unless already changed)
            if ($kantor->getJenisKantor() === $this) {
                $kantor->setJenisKantor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Unit[]
     */
    public function getUnits(): Collection|array
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units[] = $unit;
            $unit->setJenisKantor($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
            // set the owning side to null (unless already changed)
            if ($unit->getJenisKantor() === $this) {
                $unit->setJenisKantor(null);
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

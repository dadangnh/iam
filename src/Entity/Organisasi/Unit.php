<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\UnitRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"order"={"level": "ASC", "nama": "ASC"}}
 * )
 * @ORM\Entity(repositoryClass=UnitRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="unit", indexes={
 *     @ORM\Index(name="idx_unit_nama", columns={"id", "nama", "level"}),
 *     @ORM\Index(name="idx_unit_legacy", columns={"id", "legacy_kode"}),
 *     @ORM\Index(name="idx_unit_relation", columns={"id", "jenis_kantor_id", "eselon_id"}),
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "nama": "ipartial",
 *     "legacyKode": "partial",
 * })
 * @ApiFilter(DateFilter::class, properties={"tanggalAktif", "tanggalNonaktif"})
 * @ApiFilter(NumericFilter::class, properties={"level"})
 * @ApiFilter(PropertyFilter::class)
 */
class Unit
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"pegawai:read"})
     */
    private $nama;

    /**
     * @ORM\ManyToOne(targetEntity=JenisKantor::class, inversedBy="units")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $jenisKantor;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     * @Groups({"pegawai:read"})
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=Eselon::class, inversedBy="units")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $eselon;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotNull()
     */
    private $tanggalAktif;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $tanggalNonaktif;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\OneToMany(targetEntity=JabatanPegawai::class, mappedBy="unit")
     */
    private $jabatanPegawais;

    /**
     * @ORM\ManyToMany(targetEntity=Jabatan::class, mappedBy="units")
     */
    private $jabatans;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="units")
     */
    private $roles;

    public function __construct()
    {
        $this->jabatanPegawais = new ArrayCollection();
        $this->jabatans = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nama;
    }

    public function getId(): UuidInterface
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

    public function getJenisKantor(): ?JenisKantor
    {
        return $this->jenisKantor;
    }

    public function setJenisKantor(?JenisKantor $jenisKantor): self
    {
        $this->jenisKantor = $jenisKantor;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getEselon(): ?Eselon
    {
        return $this->eselon;
    }

    public function setEselon(?Eselon $eselon): self
    {
        $this->eselon = $eselon;

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
     * @return Collection|JabatanPegawai[]
     */
    public function getJabatanPegawais(): Collection
    {
        return $this->jabatanPegawais;
    }

    public function addJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if (!$this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais[] = $jabatanPegawai;
            $jabatanPegawai->setUnit($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getUnit() === $this) {
                $jabatanPegawai->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Jabatan[]
     */
    public function getJabatans(): Collection
    {
        return $this->jabatans;
    }

    public function addJabatan(Jabatan $jabatan): self
    {
        if (!$this->jabatans->contains($jabatan)) {
            $this->jabatans[] = $jabatan;
            $jabatan->addUnit($this);
        }

        return $this;
    }

    public function removeJabatan(Jabatan $jabatan): self
    {
        if ($this->jabatans->contains($jabatan)) {
            $this->jabatans->removeElement($jabatan);
            $jabatan->removeUnit($this);
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
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

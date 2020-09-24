<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\JabatanRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=JabatanRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="jabatan", indexes={
 *     @ORM\Index(name="idx_jabatan_nama_status", columns={"id", "nama", "level", "jenis"}),
 *     @ORM\Index(name="idx_jabatan_legacy", columns={"id", "legacy_kode"}),
 *     @ORM\Index(name="idx_jabatan_relation", columns={"id", "eselon_id"}),
 * })
 */
class Jabatan
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
     */
    private $nama;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $jenis;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sk;

    /**
     * @ORM\ManyToOne(targetEntity=Eselon::class, inversedBy="jabatans")
     */
    private $eselon;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $legacyKodeJabKeu;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $legacyKodeGradeKeu;

    /**
     * @ORM\OneToMany(targetEntity=JabatanPegawai::class, mappedBy="jabatan", orphanRemoval=true)
     */
    private $jabatanPegawais;

    /**
     * @ORM\ManyToMany(targetEntity=Unit::class, inversedBy="jabatans")
     */
    private $units;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="jabatans")
     */
    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity=GroupJabatan::class, inversedBy="jabatans")
     */
    private $groupJabatan;

    public function __construct()
    {
        $this->jabatanPegawais = new ArrayCollection();
        $this->units = new ArrayCollection();
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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getJenis(): ?string
    {
        return $this->jenis;
    }

    public function setJenis(string $jenis): self
    {
        $this->jenis = $jenis;

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

    public function getSk(): ?string
    {
        return $this->sk;
    }

    public function setSk(?string $sk): self
    {
        $this->sk = $sk;

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

    public function getLegacyKode(): ?string
    {
        return $this->legacyKode;
    }

    public function setLegacyKode(?string $legacyKode): self
    {
        $this->legacyKode = $legacyKode;

        return $this;
    }

    public function getLegacyKodeJabKeu(): ?string
    {
        return $this->legacyKodeJabKeu;
    }

    public function setLegacyKodeJabKeu(?string $legacyKodeJabKeu): self
    {
        $this->legacyKodeJabKeu = $legacyKodeJabKeu;

        return $this;
    }

    public function getLegacyKodeGradeKeu(): ?string
    {
        return $this->legacyKodeGradeKeu;
    }

    public function setLegacyKodeGradeKeu(?string $legacyKodeGradeKeu): self
    {
        $this->legacyKodeGradeKeu = $legacyKodeGradeKeu;

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
            $jabatanPegawai->setJabatan($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getJabatan() === $this) {
                $jabatanPegawai->setJabatan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Unit[]
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units[] = $unit;
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
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

    public function getGroupJabatan(): ?GroupJabatan
    {
        return $this->groupJabatan;
    }

    public function setGroupJabatan(?GroupJabatan $groupJabatan): self
    {
        $this->groupJabatan = $groupJabatan;

        return $this;
    }
}

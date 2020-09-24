<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Repository\Organisasi\KantorRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=KantorRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="kantor", indexes={
 *     @ORM\Index(name="idx_kantor_nama_status", columns={"id", "nama", "level", "sk"}),
 *     @ORM\Index(name="idx_kantor_legacy", columns={"id", "legacy_kode", "legacy_kode_kpp", "legacy_kode_kanwil"}),
 *     @ORM\Index(name="idx_kantor_relation", columns={"id", "jenis_kantor_id", "parent_id_id", "level"}),
 *     @ORM\Index(name="idx_kantor_location", columns={"id", "latitude", "longitude"}),
 * })
 */
class Kantor
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
     * @ORM\ManyToOne(targetEntity=JenisKantor::class, inversedBy="kantors")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $jenisKantor;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=Kantor::class, inversedBy="childIds")
     */
    private $parentId;

    /**
     * @ORM\OneToMany(targetEntity=Kantor::class, mappedBy="parentId")
     */
    private $childIds;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotNull
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $alamat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $zonaWaktu;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $legacyKodeKpp;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $legacyKodeKanwil;

    /**
     * @ORM\OneToMany(targetEntity=JabatanPegawai::class, mappedBy="kantor", orphanRemoval=true)
     */
    private $jabatanPegawais;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="kantors")
     */
    private $roles;

    public function __construct()
    {
        $this->childIds = new ArrayCollection();
        $this->jabatanPegawais = new ArrayCollection();
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

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getParentId(): ?self
    {
        return $this->parentId;
    }

    public function setParentId(?self $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildIds(): Collection
    {
        return $this->childIds;
    }

    public function addChildId(self $childId): self
    {
        if (!$this->childIds->contains($childId)) {
            $this->childIds[] = $childId;
            $childId->setParentId($this);
        }

        return $this;
    }

    public function removeChildId(self $childId): self
    {
        if ($this->childIds->contains($childId)) {
            $this->childIds->removeElement($childId);
            // set the owning side to null (unless already changed)
            if ($childId->getParentId() === $this) {
                $childId->setParentId(null);
            }
        }

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

    public function getAlamat(): ?string
    {
        return $this->alamat;
    }

    public function setAlamat(?string $alamat): self
    {
        $this->alamat = $alamat;

        return $this;
    }

    public function getTelp(): ?string
    {
        return $this->telp;
    }

    public function setTelp(?string $telp): self
    {
        $this->telp = $telp;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getZonaWaktu(): ?string
    {
        return $this->zonaWaktu;
    }

    public function setZonaWaktu(?string $zonaWaktu): self
    {
        $this->zonaWaktu = $zonaWaktu;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

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

    public function getLegacyKodeKpp(): ?string
    {
        return $this->legacyKodeKpp;
    }

    public function setLegacyKodeKpp(?string $legacyKodeKpp): self
    {
        $this->legacyKodeKpp = $legacyKodeKpp;

        return $this;
    }

    public function getLegacyKodeKanwil(): ?string
    {
        return $this->legacyKodeKanwil;
    }

    public function setLegacyKodeKanwil(?string $legacyKodeKanwil): self
    {
        $this->legacyKodeKanwil = $legacyKodeKanwil;

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
            $jabatanPegawai->setKantor($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getKantor() === $this) {
                $jabatanPegawai->setKantor(null);
            }
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

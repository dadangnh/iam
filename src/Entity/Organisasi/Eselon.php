<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Repository\Organisasi\EselonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=EselonRepository::class)
 */
class Eselon
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
     */
    private $nama;

    /**
     * @ORM\Column(type="integer")
     */
    private $tingkat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\OneToMany(targetEntity=Unit::class, mappedBy="eselon")
     */
    private $units;

    /**
     * @ORM\OneToMany(targetEntity=Jabatan::class, mappedBy="eselon")
     */
    private $jabatans;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, inversedBy="eselons")
     */
    private $roles;

    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->jabatans = new ArrayCollection();
        $this->roles = new ArrayCollection();
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

    public function getTingkat(): ?int
    {
        return $this->tingkat;
    }

    public function setTingkat(int $tingkat): self
    {
        $this->tingkat = $tingkat;

        return $this;
    }

    public function getKode(): ?string
    {
        return $this->kode;
    }

    public function setKode(string $kode): self
    {
        $this->kode = $kode;

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
            $unit->setEselon($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
            // set the owning side to null (unless already changed)
            if ($unit->getEselon() === $this) {
                $unit->setEselon(null);
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
            $jabatan->setEselon($this);
        }

        return $this;
    }

    public function removeJabatan(Jabatan $jabatan): self
    {
        if ($this->jabatans->contains($jabatan)) {
            $this->jabatans->removeElement($jabatan);
            // set the owning side to null (unless already changed)
            if ($jabatan->getEselon() === $this) {
                $jabatan->setEselon(null);
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

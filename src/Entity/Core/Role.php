<?php

namespace App\Entity\Core;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\Unit;
use App\Entity\User\User;
use App\Repository\Core\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
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
     * @ORM\Column(type="string", length=255)
     */
    private $systemName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $deskripsi;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="containRoles")
     */
    private $subsOfRole;

    /**
     * @ORM\OneToMany(targetEntity=Role::class, mappedBy="subsOfRole")
     */
    private $containRoles;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="roles")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=Jabatan::class, mappedBy="roles")
     */
    private $jabatans;

    /**
     * @ORM\ManyToMany(targetEntity=Unit::class, mappedBy="roles")
     */
    private $units;

    /**
     * @ORM\ManyToMany(targetEntity=Kantor::class, mappedBy="roles")
     */
    private $kantors;

    /**
     * @ORM\ManyToMany(targetEntity=Eselon::class, mappedBy="roles")
     */
    private $eselons;

    public function __construct()
    {
        $this->containRoles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->jabatans = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->kantors = new ArrayCollection();
        $this->eselons = new ArrayCollection();
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

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): self
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getDeskripsi(): ?string
    {
        return $this->deskripsi;
    }

    public function setDeskripsi(?string $deskripsi): self
    {
        $this->deskripsi = $deskripsi;

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

    public function getSubsOfRole(): ?self
    {
        return $this->subsOfRole;
    }

    public function setSubsOfRole(?self $subsOfRole): self
    {
        $this->subsOfRole = $subsOfRole;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getContainRoles(): Collection
    {
        return $this->containRoles;
    }

    public function addContainRole(self $containRole): self
    {
        if (!$this->containRoles->contains($containRole)) {
            $this->containRoles[] = $containRole;
            $containRole->setSubsOfRole($this);
        }

        return $this;
    }

    public function removeContainRole(self $containRole): self
    {
        if ($this->containRoles->contains($containRole)) {
            $this->containRoles->removeElement($containRole);
            // set the owning side to null (unless already changed)
            if ($containRole->getSubsOfRole() === $this) {
                $containRole->setSubsOfRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeRole($this);
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
            $jabatan->addRole($this);
        }

        return $this;
    }

    public function removeJabatan(Jabatan $jabatan): self
    {
        if ($this->jabatans->contains($jabatan)) {
            $this->jabatans->removeElement($jabatan);
            $jabatan->removeRole($this);
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
            $unit->addRole($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
            $unit->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Kantor[]
     */
    public function getKantors(): Collection
    {
        return $this->kantors;
    }

    public function addKantor(Kantor $kantor): self
    {
        if (!$this->kantors->contains($kantor)) {
            $this->kantors[] = $kantor;
            $kantor->addRole($this);
        }

        return $this;
    }

    public function removeKantor(Kantor $kantor): self
    {
        if ($this->kantors->contains($kantor)) {
            $this->kantors->removeElement($kantor);
            $kantor->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Eselon[]
     */
    public function getEselons(): Collection
    {
        return $this->eselons;
    }

    public function addEselon(Eselon $eselon): self
    {
        if (!$this->eselons->contains($eselon)) {
            $this->eselons[] = $eselon;
            $eselon->addRole($this);
        }

        return $this;
    }

    public function removeEselon(Eselon $eselon): self
    {
        if ($this->eselons->contains($eselon)) {
            $this->eselons->removeElement($eselon);
            $eselon->removeRole($this);
        }

        return $this;
    }
}

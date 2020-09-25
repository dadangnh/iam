<?php

namespace App\Entity\Core;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\Unit;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Core\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"order"={"level": "ASC", "nama": "ASC"}}
 * )
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ORM\Table(name="role", indexes={
 *     @ORM\Index(name="idx_role_nama_status", columns={"id", "nama", "system_name", "jenis"}),
 *     @ORM\Index(name="idx_role_relation", columns={"id", "level", "subs_of_role_id"}),
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "nama": "ipartial",
 *     "systemName": "ipartial",
 *     "deskripsi": "ipartial",
 * })
 * @ApiFilter(NumericFilter::class, properties={"level", "jenis"})
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
     * @Assert\NotBlank()
     */
    private $nama;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
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
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="role", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_user",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *     }
     * )
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=Jabatan::class, inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_jabatan",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="jabatan_id", referencedColumnName="id")
     *     }
     * )
     */
    private $jabatans;

    /**
     * @ORM\ManyToMany(targetEntity=Unit::class, inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_unit",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     *     }
     * )
     */
    private $units;

    /**
     * @ORM\ManyToMany(targetEntity=Kantor::class, inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_kantor",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="kantor_id", referencedColumnName="id")
     *     }
     * )
     */
    private $kantors;

    /**
     * @ORM\ManyToMany(targetEntity=Eselon::class, inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_eselon",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="eselon_id", referencedColumnName="id")
     *     }
     * )
     */
    private $eselons;

    /**
     * @ORM\ManyToMany(targetEntity=JenisKantor::class, inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_jenis_kantor",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="jenis_kantor_id", referencedColumnName="id")
     *     }
     * )
     */
    private $jenisKantors;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="role_group",
     *     joinColumns={
     *          @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *     }
     * )
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity=Permission::class, mappedBy="roles")
     */
    private $permissions;

    /**
     * @ORM\Column(type="integer", options={
     *     "comment":"Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
     *          6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
     *          10 => jabatan + unit + kantor"})
     */
    private $jenis;

    public function __construct()
    {
        $this->containRoles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->jabatans = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->kantors = new ArrayCollection();
        $this->eselons = new ArrayCollection();
        $this->jenisKantors = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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

    /**
     * @return Collection|JenisKantor[]
     */
    public function getJenisKantors(): Collection
    {
        return $this->jenisKantors;
    }

    public function addJenisKantor(JenisKantor $jenisKantor): self
    {
        if (!$this->jenisKantors->contains($jenisKantor)) {
            $this->jenisKantors[] = $jenisKantor;
            $jenisKantor->addRole($this);
        }

        return $this;
    }

    public function removeJenisKantor(JenisKantor $jenisKantor): self
    {
        if ($this->jenisKantors->contains($jenisKantor)) {
            $this->jenisKantors->removeElement($jenisKantor);
            $jenisKantor->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addRole($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->addRole($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            $permission->removeRole($this);
        }

        return $this;
    }

    public function getJenis(): ?int
    {
        return $this->jenis;
    }

    public function setJenis(int $jenis): self
    {
        $this->jenis = $jenis;

        return $this;
    }
}

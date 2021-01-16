<?php

namespace App\Entity\Core;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
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
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *          "order"={"level": "ASC", "nama": "ASC"},
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
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ORM\Table(name="role", indexes={
 *     @ORM\Index(name="idx_role_nama_status", columns={"id", "nama", "system_name", "jenis"}),
 *     @ORM\Index(name="idx_role_relation", columns={"id", "level", "subs_of_role_id"}),
 * })
 * Disable second level cache for further analysis
 * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @UniqueEntity(fields={"nama"})
 * @UniqueEntity(fields={"systemName"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "nama": "ipartial",
 *     "systemName": "ipartial",
 *     "deskripsi": "ipartial",
 * })
 * @ApiFilter(NumericFilter::class, properties={"level", "jenis"})
 * @ApiFilter(PropertyFilter::class)
 */
class Role
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
     * @Assert\NotBlank()
     */
    private ?string $systemName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private ?string $deskripsi;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private ?int $level;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="containRoles")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
     */
    private $subsOfRole;

    /**
     * @ORM\OneToMany(targetEntity=Role::class, mappedBy="subsOfRole")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
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
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity=Permission::class, mappedBy="roles")
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $permissions;

    /**
     * @ORM\Column(type="integer", options={
     *     "comment":"Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
     *          6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
     *          10 => jabatan + unit + kantor"})
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $jenis;

    #[Pure] public function __construct()
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
    public function getContainRoles(): Collection|array
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
    public function getUsers(): Collection|array
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
    public function getJabatans(): Collection|array
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
    public function getUnits(): Collection|array
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
    public function getKantors(): Collection|array
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
    public function getEselons(): Collection|array
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
    public function getJenisKantors(): Collection|array
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
    public function getGroups(): Collection|array
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
    public function getPermissions(): Collection|array
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

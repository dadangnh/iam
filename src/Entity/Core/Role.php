<?php

namespace App\Entity\Core;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Entity\Aplikasi\Aplikasi;
use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanLuar;
use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\KantorLuar;
use App\Entity\Organisasi\Unit;
use App\Entity\Organisasi\UnitLuar;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Core\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role Class
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_USER")',
            securityMessage: 'Only a valid user can access this.'
        ),
        new Put(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        ),
        new Patch(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        ),
        new Delete(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            securityMessage: 'Only a valid user can access this.'
        ),
        new Post(
            security: 'is_granted("ROLE_APLIKASI") or is_granted("ROLE_ADMIN") or is_granted("ROLE_UPK_PUSAT")',
            securityMessage: 'Only admin/app can add new resource to this entity type.'
        )
    ],
    normalizationContext: [
        'groups' => [
            'pegawai:read',
            'role:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'pegawai:write',
            'role:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    order: [
        'level' => 'ASC',
        'nama' => 'ASC'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: RoleRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'role'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'system_name',
        'jenis'
    ],
    name: 'idx_role_nama_status'
)]
#[ORM\Index(
    columns: [
        'id',
        'level',
        'subs_of_role_id'
    ],
    name: 'idx_role_relation'
)]
#[ORM\Index(
    columns: [
        'id',
        'nama',
        'system_name',
        'jenis',
        'start_date',
        'end_date'
    ],
    name: 'idx_role_date'
)]
#[UniqueEntity(
    fields: [
        'nama',
        'systemName'
    ]
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'nama' => 'ipartial',
        'systemName' => 'ipartial',
        'deskripsi' => 'ipartial'
    ]
)]
#[ApiFilter(
    filterClass: NumericFilter::class,
    properties: [
        'level',
        'jenis'
    ]
)]
#[ApiFilter(
    filterClass: DateFilter::class,
    properties: [
        'startDate',
        'endDate'
    ]
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'Operator',
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class Role
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private UuidV4 $id;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'pegawai:read',
            'role:read',
            'role:write'
        ]
    )]
    private ?string $nama;

    #[ORM\Column(
        type: Types::STRING,
        length: 255
    )]
    #[Assert\NotBlank]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?string $systemName;

    #[ORM\Column(
        type: Types::TEXT,
        nullable: true
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?string $deskripsi;

    #[ORM\Column(
        type: Types::INTEGER,
        nullable: true
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?int $level;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToOne(
        targetEntity: Role::class,
        inversedBy: 'containRoles'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?Role $subsOfRole;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\OneToMany(
        mappedBy: 'subsOfRole',
        targetEntity: Role::class
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $containRoles;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: User::class,
        inversedBy: 'role',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_user'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'user_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $users;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: Jabatan::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_jabatan'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'jabatan_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $jabatans;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: JabatanLuar::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_jabatan_luar'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'jabatan_luar_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $jabatanLuars;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: Unit::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_unit'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'unit_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $units;

    #[ORM\ManyToMany(
        targetEntity: UnitLuar::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_unit_luar'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'unit_luar_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $unitLuars;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: Kantor::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_kantor'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'kantor_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $kantors;

    #[ORM\ManyToMany(
        targetEntity: KantorLuar::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_kantor_luar'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'kantor_luar_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $kantorLuars;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: Eselon::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_eselon'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'eselon_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $eselons;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: JenisKantor::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_jenis_kantor'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'jenis_kantor_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $jenisKantors;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: Group::class,
        inversedBy: 'roles',
        cascade: [
            'persist'
        ]
    )]
    #[ORM\JoinTable(
        name: 'role_group'
    )]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id'
    )]
    #[ORM\InverseJoinColumn(
        name: 'group_id',
        referencedColumnName: 'id'
    )]
    #[Assert\Valid]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $groups;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToMany(
        targetEntity: Permission::class,
        mappedBy: 'roles'
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $permissions;

    #[ORM\Column(
        type: Types::INTEGER,
        options: [
            'comment' => 'Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon, 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor, 10 => jabatan + unit + kantor, 11 => jabatan + unit + jenis kantor'
        ]
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?int $jenis;

    #[ORM\Column(
        type: Types::DATE_IMMUTABLE
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(
        type: Types::DATE_IMMUTABLE,
        nullable: true
    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(
        type: Types::BOOLEAN,
        nullable: true,
        options: [
            'default' => false
        ]

    )]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private ?bool $Operator = false;

    #[ORM\ManyToMany(targetEntity: Aplikasi::class)]
    #[Groups(
        groups: [
            'role:read',
            'role:write'
        ]
    )]
    private Collection $Aplikasis;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->containRoles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->jabatans = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->kantors = new ArrayCollection();
        $this->eselons = new ArrayCollection();
        $this->jenisKantors = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->jabatanLuars = new ArrayCollection();
        $this->kantorLuars = new ArrayCollection();
        $this->unitLuars = new ArrayCollection();
        $this->Aplikasis = new ArrayCollection();
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
     * @return Collection|JabatanLuar[]
     */
    public function getJabatanLuars(): Collection
    {
        return $this->jabatanLuars;
    }

    public function addJabatanLuar(JabatanLuar $jabatanLuar): static
    {
        if (!$this->jabatanLuars->contains($jabatanLuar)) {
            $this->jabatanLuars[] = $jabatanLuar;
            $jabatanLuar->addRole($this);
        }

        return $this;
    }

    public function removeJabatanLuar(JabatanLuar $jabatanLuar): static
    {
        if ($this->jabatanLuars->contains($jabatanLuar)) {
            $this->jabatanLuars->removeElement($jabatanLuar);
            $jabatanLuar->removeRole($this);
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
     * @return Collection|UnitLuar[]
     */
    public function getUnitLuars(): Collection|array
    {
        return $this->unitLuars;
    }

    public function addUnitLuar(UnitLuar $unitLuar): self
    {
        if (!$this->unitLuars->contains($unitLuar)) {
            $this->unitLuars[] = $unitLuar;
            $unitLuar->addRole($this);
        }

        return $this;
    }

    public function removeUnitLuar(UnitLuar $unitLuar): self
    {
        if ($this->unitLuars->contains($unitLuar)) {
            $this->unitLuars->removeElement($unitLuar);
            $unitLuar->removeRole($this);
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
     * @return Collection|KantorLuar[]
     */
    public function getKantorLuars(): Collection|array
    {
        return $this->kantorLuars;
    }

    public function addKantorLuar(KantorLuar $kantorLuar): self
    {
        if (!$this->kantorLuars->contains($kantorLuar)) {
            $this->kantorLuars[] = $kantorLuar;
            $kantorLuar->addRole($this);

        }

        return $this;
    }

    public function removeKantorLuar(KantorLuar $kantorLuar): static
    {
        if ($this->kantorLuars->contains($kantorLuar)) {
            $this->kantorLuars->removeElement($kantorLuar);
            $kantorLuar->removeRole($this);
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

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    #[ORM\PrePersist]
    public function setStartDateValue(): void
    {
        // Only create start date if no date provided
        if (!isset($this->startDate)) {
            $this->startDate = new \DateTimeImmutable();
        }
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isOperator(): ?bool
    {
        return $this->Operator;
    }

    public function setOperator(?bool $Operator): self
    {
        $this->Operator = $Operator;

        return $this;
    }

    /**
     * @return Collection|Aplikasi[]
     */
    public function getAplikasis(): Collection|array
    {
        return $this->Aplikasis;
    }

    public function addAplikasi(Aplikasi $aplikasi): self
    {
        if (!$this->Aplikasis->contains($aplikasi)) {
            $this->Aplikasis[] = $aplikasi;
//            $aplikasi->addRole($this);
        }

        return $this;
    }

    public function removeAplikasi(Aplikasi $aplikasi): self
    {
        if ($this->Aplikasis->contains($aplikasi)) {
            $this->Aplikasis->removeElement($aplikasi);
//            $aplikasi->removeRole($this);
        }

        return $this;
    }
}

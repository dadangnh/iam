<?php

namespace App\Entity\User;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\JabatanPegawaiLuar;
use App\Entity\Pegawai\Pegawai;
use App\Entity\Pegawai\PegawaiLuar;
use App\Helper\RoleHelper;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User Class
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
            'user:read'
        ],
        'swagger_definition_name' => 'read'
    ],
    denormalizationContext: [
        'groups' => [
            'user:write'
        ],
        'swagger_definition_name' => 'write'
    ],
    security: 'is_granted("ROLE_USER")',
    securityMessage: 'Only a valid user can access this.'
)]
#[ORM\Entity(
    repositoryClass: UserRepository::class
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: '`user`'
)]
#[ORM\Index(
    columns: [
        'id',
        'username',
        'password'
    ],
    name: 'idx_user_data'
)]
#[ORM\Index(
    columns: [
        'id',
        'active',
        'locked'
    ],
    name: 'idx_user_active'
)]
#[ORM\Index(
    columns: [
        'id',
        'username',
        'service_account'
    ],
    name: 'idx_user_service_account'
)]
#[UniqueEntity(
    fields: [
        'username'
    ]
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id' => 'exact',
        'username' => 'exact',
        'pegawai.nama' => 'ipartial',
        'pegawai.nip9' => 'partial',
        'pegawai.nip18' => 'partial'
    ]
)]
#[ApiFilter(
    filterClass: DateFilter::class,
    properties: [
        'lastChange'
    ]
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'active',
        'locked',
        'serviceAccount'
    ]
)]
#[ApiFilter(
    filterClass: PropertyFilter::class
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(
        type: 'uuid',
        unique: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private UuidV4 $id;

    #[ORM\Column(
        type: Types::STRING,
        length: 180,
        unique: true
    )]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 150,
        maxMessage: 'username cannot less than 3 char and more than 150 char.'
    )]
    #[Groups(
        groups: [
            'user:read',
            'user:write',
            'pegawai:read'
        ]
    )]
    private string $username;

    #[ORM\ManyToMany(
        targetEntity: Role::class,
        mappedBy: 'users'
    )]
    private Collection $role;

    /**
     * Default Symfony Guard Role
     * This is a virtual attributes
     * @var null|array
     */
    #[Groups(
        groups: [
            'user:read',
            'pegawai:read'
        ]
    )]
    private ?array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(
        type: Types::STRING
    )]
    private string $password;

    /**
     * @var null|string plain password
     */
    #[Assert\Length(
        min: 5,
        max: 128
    )]
    private ?string $plainPassword = null;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private ?bool $active;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private ?bool $locked;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'user:write'
        ]
    )]
    private ?bool $twoFactorEnabled;

    #[ORM\Column(
        type: Types::DATETIME_MUTABLE
    )]
    #[Groups(
        groups: [
            'user:write'
        ]
    )]
    private ?DateTimeInterface $lastChange;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: UserTwoFactor::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'user:write'
        ]
    )]
    private Collection $userTwoFactors;

    #[ORM\OneToOne(
        mappedBy: 'user',
        targetEntity: Pegawai::class,
        cascade: ['persist', 'remove']
    )]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private ?Pegawai $pegawai;

    #[ORM\OneToOne(
        mappedBy: 'userLuar',
        cascade: ['persist', 'remove'])]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private ?PegawaiLuar $pegawaiLuar;

    #[ORM\OneToMany(
        mappedBy: 'owner',
        targetEntity: Group::class
    )]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private Collection $ownedGroups;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: GroupMember::class,
        orphanRemoval: true
    )]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private Collection $groupMembers;

    #[ORM\Column(
        type: Types::BOOLEAN
    )]
    #[Assert\NotNull]
    #[Groups(
        groups: [
            'user:read',
            'user:write'
        ]
    )]
    private ?bool $serviceAccount = false;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->userTwoFactors = new ArrayCollection();
        $this->role = new ArrayCollection();
        $this->ownedGroups = new ArrayCollection();
        $this->groupMembers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @throws Exception
     */
    public function getCustomRoles(ObjectManager $objectManager): array
    {
        // Get Direct Role Relation
        $plainRoles = $this->getDirectRoles();

        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor, 11 => jabatan + unit + jenis kantor"
        $arrayOfRoles = [];
        $arrayOfRolesLuar = [];
        if (null !== $this->getPegawai()) {
            // If on leave, assign ROLE_ON_LEAVE
            if ($this->getPegawai()->getOnLeave()) {
                $arrayOfRoles[] = ['ROLE_ON_LEAVE'];
            }

            // make sure that retired person doesn't get role
            if (!$this->getPegawai()->getPensiun()) {
                /** @var JabatanPegawai $jabatanPegawai */
                foreach ($this->getPegawai()->getJabatanPegawais() as $jabatanPegawai) {
                    // Only process active jabatans
                    if ($jabatanPegawai->getTanggalMulai() <= new DateTimeImmutable('now')
                        && ($jabatanPegawai->getTanggalSelesai() >= new DateTimeImmutable('now')
                            || null === $jabatanPegawai->getTanggalSelesai())
                    ) {
                        $arrayOfRoles[] = array_values(
                            RoleHelper::getRolesFromJabatanPegawai($objectManager, $jabatanPegawai)
                        );
                        $arrayOfRoles[] = array_values($this->roles);
                    }
                }
            } else {
                return ['ROLE_RETIRED'];
            }
        }
        if (null !== $this->getPegawaiLuar()) {
            // make sure that retired person doesn't get role
            if (!$this->getPegawaiLuar()->getPensiun()) {
                /** @var JabatanPegawaiLuar $jabatanPegawaiLuar */
                foreach ($this->getPegawaiLuar()->getJabatanPegawaiLuars() as $jabatanPegawaiLuar) {
                    // Only process active jabatans
                    if ($jabatanPegawaiLuar->getTanggalMulai() <= new DateTimeImmutable('now')
                        && ($jabatanPegawaiLuar->getTanggalSelesai() >= new DateTimeImmutable('now')
                            || null === $jabatanPegawaiLuar->getTanggalSelesai())
                    ) {
                        $arrayOfRolesLuar[] = array_values(
                            RoleHelper::getRolesFromJabatanPegawaiLuar($objectManager, $jabatanPegawaiLuar)
                        );
                        $arrayOfRolesLuar[] = array_values($this->roles);
                    }
                }
            } else {
                return ['ROLE_LUAR_RETIRED'];
            }
        }
        $plainRoles = array_values(array_merge($plainRoles, ...$arrayOfRoles, ...$arrayOfRolesLuar));

        return array_values(array_unique($plainRoles));
    }


    /**
     * @see UserInterface
     */
    public function getRole(): array
    {
        $roles = $this->role->toArray();

        return array_unique($roles);
    }

    /**
     * Method to get the direct relation of role and user
     * @return array
     */
    public function getDirectRoles(): array
    {
        // check whether user is still active or not
        if ($this->isActive()) {
            // for active user, give a normal ROLE_USER for every user
            $plainRoles[] = 'ROLE_USER';

            // get the direct role <=> user relation
            $roles = $this->getRole();

            /** @var Role $role */
            foreach ($roles as $role) {
                // make sure only direct Role <=> User relation are considered here (only type 1)
                if ((1 === $role->getJenis())
                    && $role->getStartDate() <= new DateTimeImmutable('now')
                    && ($role->getEndDate() >= new DateTimeImmutable('now')
                        || null === $role->getEndDate())
                ) {
                    $plainRoles[] = $role->getNama();
                }
            }

            // If service account, assign ROLE_SERVICE_ACCOUNT
            if ($this->isServiceAccount()) {
                $plainRoles[] = 'ROLE_SERVICE_ACCOUNT';
            }

            // return in unique array
            return array_unique($plainRoles);
        }

        // for inactive user, give inactive role
        return ['ROLE_INACTIVE'];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword = $password;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getTwoFactorEnabled(): ?bool
    {
        return $this->twoFactorEnabled;
    }

    public function setTwoFactorEnabled(bool $twoFactorEnabled): self
    {
        $this->twoFactorEnabled = $twoFactorEnabled;

        return $this;
    }

    public function getLastChange(): ?DateTimeInterface
    {
        return $this->lastChange;
    }

    public function setLastChange(DateTimeInterface $lastChange): self
    {
        $this->lastChange = $lastChange;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setLastChangeValue(): void
    {
        $this->lastChange = new DateTimeImmutable('now');
    }

    /**
     * @return Collection|UserTwoFactor[]
     */
    public function getUserTwoFactors(): Collection|array
    {
        return $this->userTwoFactors;
    }

    public function addUserTwoFactor(UserTwoFactor $userTwoFactor): self
    {
        if (!$this->userTwoFactors->contains($userTwoFactor)) {
            $this->userTwoFactors[] = $userTwoFactor;
            $userTwoFactor->setUser($this);
        }

        return $this;
    }

    public function removeUserTwoFactor(UserTwoFactor $userTwoFactor): self
    {
        if ($this->userTwoFactors->contains($userTwoFactor)) {
            $this->userTwoFactors->removeElement($userTwoFactor);
            // set the owning side to null (unless already changed)
            if ($userTwoFactor->getUser() === $this) {
                $userTwoFactor->setUser(null);
            }
        }

        return $this;
    }

    public function getPegawai(): ?Pegawai
    {
        return $this->pegawai;
    }

    public function setPegawai(Pegawai $pegawai): self
    {
        $this->pegawai = $pegawai;

        // set the owning side of the relation if necessary
        if ($pegawai->getUser() !== $this) {
            $pegawai->setUser($this);
        }

        return $this;
    }

    public function getPegawaiLuar(): ?PegawaiLuar
    {
        return $this->pegawaiLuar;
    }

    public function setPegawaiLuar(?PegawaiLuar $pegawaiLuar): self
    {
        // unset the owning side of the relation if necessary
//        if ($pegawaiLuar === null && $this->pegawaiLuar !== null) {
//            $this->pegawaiLuar->setUserLuar(null);
//        }
//
//        // set the owning side of the relation if necessary
//        if ($pegawaiLuar !== null && $pegawaiLuar->getUserLuar() !== $this) {
//            $pegawaiLuar->setUserLuar($this);
//        }
//
//        $this->pegawaiLuar = $pegawaiLuar;

        $this->pegawaiLuar = $pegawaiLuar;

        // set the owning side of the relation if necessary
        if ($pegawaiLuar->getUserLuar() !== $this) {
            $pegawaiLuar->setUserLuar($this);
        }

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->role->contains($role)) {
            $this->role[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->role->contains($role)) {
            $this->role->removeElement($role);
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getOwnedGroups(): Collection|array
    {
        return $this->ownedGroups;
    }

    public function addOwnedGroup(Group $ownedGroup): self
    {
        if (!$this->ownedGroups->contains($ownedGroup)) {
            $this->ownedGroups[] = $ownedGroup;
            $ownedGroup->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedGroup(Group $ownedGroup): self
    {
        if ($this->ownedGroups->contains($ownedGroup)) {
            $this->ownedGroups->removeElement($ownedGroup);
            // set the owning side to null (unless already changed)
            if ($ownedGroup->getOwner() === $this) {
                $ownedGroup->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GroupMember[]
     */
    public function getGroupMembers(): Collection|array
    {
        return $this->groupMembers;
    }

    public function addGroupMember(GroupMember $groupMember): self
    {
        if (!$this->groupMembers->contains($groupMember)) {
            $this->groupMembers[] = $groupMember;
            $groupMember->setUser($this);
        }

        return $this;
    }

    public function removeGroupMember(GroupMember $groupMember): self
    {
        if ($this->groupMembers->contains($groupMember)) {
            $this->groupMembers->removeElement($groupMember);
            // set the owning side to null (unless already changed)
            if ($groupMember->getUser() === $this) {
                $groupMember->setUser(null);
            }
        }
        return $this;
    }

    public function isServiceAccount(): ?bool
    {
        return $this->serviceAccount;
    }

    public function setServiceAccount(bool $serviceAccount): self
    {
        $this->serviceAccount = $serviceAccount;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function generateRoles(LifecycleEventArgs $event): array
    {
        return $this->roles = $this->getCustomRoles($event->getObjectManager());
    }
}

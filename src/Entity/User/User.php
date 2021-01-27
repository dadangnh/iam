<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\Pegawai;
use App\Repository\User\UserRepository;
use App\utils\RoleUtils;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Monolog\DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"user:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"user:write"}, "swagger_definition_name"="Write"},
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
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"username"})
 * @ORM\Table(name="`user`", indexes={
 *     @ORM\Index(name="idx_user_data", columns={"id", "username", "password"}),
 *     @ORM\Index(name="idx_user_active", columns={"id", "active", "locked"}),
 * })
 * Disable second level cache for further analysis
 * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ApiFilter(BooleanFilter::class, properties={"active", "locked"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "username": "ipartial",
 *     "pegawai.nama": "ipartial",
 *     "pegawai.nip9": "partial",
 *     "pegawai.nip18": "partial"
 * })
 * @ApiFilter(DateFilter::class, properties={"lastChange"})
 * @ApiFilter(PropertyFilter::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write", "pegawai:read"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="3",
     *     max="150",
     *     maxMessage="username tidak boleh kurang dari 3 dan lebih dari 150 karakter"
     * )
     */
    private string $username;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $role;

    /**
     * Default Symfony Guard Role
     * This is a virtual attributes
     * @var null|array
     * @Groups({"user:read", "pegawai:read"})
     */
    private ?array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @var null|string plain password
     * @Assert\Length(min=5, max=128)
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     * @Assert\NotNull()
     */
    private ?bool $active;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     * @Assert\NotNull()
     */
    private ?bool $locked;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:write"})
     * @Assert\NotNull()
     */
    private ?bool $twoFactorEnabled;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user:write"})
     */
    private ?DateTimeInterface $lastChange;

    /**
     * @ORM\OneToMany(targetEntity=UserTwoFactor::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user:write"})
     */
    private $userTwoFactors;

    /**
     * @ORM\OneToOne(targetEntity=Pegawai::class, mappedBy="user", cascade={"persist", "remove"})
     * Disable second level cache for further analysis
     * @ ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     */
    private $pegawai;

    /**
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="owner")
     * @Groups({"user:read", "user:write"})
     */
    private $ownedGroups;

    /**
     * @ORM\OneToMany(targetEntity=GroupMember::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user:read", "user:write"})
     */
    private $groupMembers;

    #[Pure] public function __construct()
    {
        $this->userTwoFactors = new ArrayCollection();
        $this->role = new ArrayCollection();
        $this->ownedGroups = new ArrayCollection();
        $this->groupMembers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function getId()
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
        // Get Direct Role Relation
        $plainRoles = $this->getDirectRoles();

        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor"
        if (null !== $this->getPegawai()) {
            $arrayOfRoles = [];
            // make sure that retired person doesn't get role
            if (!$this->getPegawai()->getPensiun()) {
                /** @var JabatanPegawai $jabatanPegawai */
                foreach ($this->getPegawai()->getJabatanPegawais() as $jabatanPegawai) {
                    $arrayOfRoles[] = RoleUtils::getPlainRolesNameFromJabatanPegawai($jabatanPegawai);
                }
            } else {
                return ['ROLE_RETIRED'];
            }
            $plainRoles = array_merge($plainRoles, ...$arrayOfRoles);
        }

        return array_unique($plainRoles);
    }

    /**
     * @see UserInterface
     */
    #[Pure] public function getRole(): array
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
                if (1 === $role->getJenis()) {
                    $plainRoles[] = $role->getNama();
                }
            }

            // return in unique array
            return array_unique($plainRoles);
        }

        // for inactive user, give inactive role
        return ['ROLE_INACTIVE'];
    }
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
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

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setLastChangeValue(): void
    {
        $this->lastChange = new DateTimeImmutable(true);
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
}

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
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Monolog\DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"user:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"user:write"}, "swagger_definition_name"="Write"},
 *     attributes={
 *          "security"="is_granted('ROLE_USER') or is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *          "security_message"="Only a valid user/admin/app can access this."
 *     },
 *     collectionOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_USER') or is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only a valid user/admin/app can access this."
 *          },
 *         "post"={
 *              "security"="is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
 *              "security_message"="Only admin/app can add new resource to this entity type."
 *          }
 *     },
 *     itemOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_USER') or is_granted('ROLE_APLIKASI') or is_granted('ROLE_ADMIN')",
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
 *     @ORM\Index(name="idx_user_status", columns={"id", "status", "locked"}),
 * })
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ApiFilter(BooleanFilter::class, properties={"status", "locked"})
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
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write", "pegawai:read"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="3",
     *     max="150",
     *     maxMessage="username tidak boleh kurang dari 3 dan lebih dari 150 karakter"
     * )
     */
    private $username;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $role;

    /**
     * Default Symfony Guard Role
     * This is a virtual attributes
     * @var array
     * @Groups({"user:read", "pegawai:read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $password;

    /**
     * @var string plain password
     * @Assert\Length(min=5, max=128)
     */
    private $plainPassword = null;

    /**
     * @ORM\Column(type="boolean")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     * @Assert\NotNull()
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     * @Assert\NotNull()
     */
    private $locked;

    /**
     * @ORM\Column(type="boolean")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:write"})
     * @Assert\NotNull()
     */
    private $twoFactorEnabled;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:write"})
     */
    private $lastChange;

    /**
     * @ORM\OneToMany(targetEntity=UserTwoFactor::class, mappedBy="user", orphanRemoval=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:write"})
     */
    private $userTwoFactors;

    /**
     * @ORM\OneToOne(targetEntity=Pegawai::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     */
    private $pegawai;

    /**
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="owner")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     */
    private $ownedGroups;

    /**
     * @ORM\OneToMany(targetEntity=GroupMember::class, mappedBy="user", orphanRemoval=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Groups({"user:read", "user:write"})
     */
    private $groupMembers;

    public function __construct()
    {
        $this->userTwoFactors = new ArrayCollection();
        $this->role = new ArrayCollection();
        $this->ownedGroups = new ArrayCollection();
        $this->groupMembers = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->username;
    }

    public function getId(): UuidInterface
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
        return (string) $this->username;
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
        $roles = $this->getRole();
        // guarantee every user at least has ROLE_USER
        $plainRoles[] = 'ROLE_USER';
        /** @var Role $role */
        foreach ($roles as $role) {
            $plainRoles[] = $role->getNama();
        }

        // Get role by jabatan pegawai
        // Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
        // 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
        // 10 => jabatan + unit + kantor"
        if (null !== $this->getPegawai()) {
            /** @var JabatanPegawai $jabatanPegawai */
            foreach ($this->getPegawai()->getJabatanPegawais() as $jabatanPegawai) {
                $jabatan = $jabatanPegawai->getJabatan();
                $unit = $jabatanPegawai->getUnit();
                $kantor = $jabatanPegawai->getKantor();

                // check from jabatan
                if (null !== $jabatan) {
                    // direct role from jabatan/ jabatan unit/ jabatan kantor/ combination
                    foreach ($jabatan->getRoles() as $role) {
                        if (2 === $role->getJenis()) {
                            $plainRoles[] = $role->getNama();
                        } elseif (8 === $role->getJenis() && $role->getUnits()->contains($unit)) {
                            $plainRoles[] = $role->getNama();
                        } elseif (9 === $role->getJenis() && $role->getKantors()->contains($kantor)) {
                            $plainRoles[] = $role->getNama();
                        } elseif (10 === $role->getJenis()
                            && $role->getUnits()->contains($unit)
                            && $role->getKantors()->contains($kantor)
                        ) {
                            $plainRoles[] = $role->getNama();
                        }
                    }

                    // get eselon level
                    $eselon = $jabatan->getEselon();
                    if (null !== $eselon) {
                        foreach ($eselon->getRoles() as $role) {
                            if (5 === $role->getJenis()) {
                                $plainRoles[] = $role->getNama();
                            }
                        }
                    }

                // get role from unit
                } elseif (null !== $unit) {
                    foreach ($unit->getRoles() as $role) {
                        if (3 === $role->getJenis()) {
                            $plainRoles[] = $role->getNama();
                        }
                    }

                    // get jenis kantor
                    $jenisKantor = $unit->getJenisKantor();
                    if (null !== $jenisKantor) {
                        foreach ($jenisKantor->getRoles() as $role) {
                            if (6 === $role->getJenis()) {
                                $plainRoles[] = $role->getNama();
                            }
                        }
                    }

                // get role from kantor
                } elseif (null !== $kantor) {
                    foreach ($kantor->getRoles() as $role) {
                        if (4 === $role->getJenis()) {
                            $plainRoles[] = $role->getNama();
                        }
                    }

                    // get jenis kantor
                    $jenisKantor = $kantor->getJenisKantor();
                    if (null !== $jenisKantor) {
                        foreach ($jenisKantor->getRoles() as $role) {
                            if (6 === $role->getJenis()) {
                                $plainRoles[] = $role->getNama();
                            }
                        }
                    }
                }
            }
        }

        return array_unique($plainRoles);
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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
    public function getUserTwoFactors(): Collection
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
    public function getOwnedGroups(): Collection
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
    public function getGroupMembers(): Collection
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

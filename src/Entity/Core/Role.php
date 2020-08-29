<?php

namespace App\Entity\Core;

use ApiPlatform\Core\Annotation\ApiResource;
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

    public function __construct()
    {
        $this->containRoles = new ArrayCollection();
        $this->users = new ArrayCollection();
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
}

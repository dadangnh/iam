<?php

namespace App\Entity\Aplikasi;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Core\Permission;
use App\Repository\Aplikasi\ModulRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ModulRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="modul", indexes={
 *     @ORM\Index(name="idx_modul_nama_status", columns={"nama", "system_name", "status"}),
 *     @ORM\Index(name="idx_modul_nama_aplikasi", columns={"aplikasi_id", "nama", "system_name"}),
 * })
 */
class Modul
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
     * @ORM\ManyToOne(targetEntity=Aplikasi::class, inversedBy="moduls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aplikasi;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nama;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $systemName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $deskripsi;

    /**
     * @ORM\ManyToMany(targetEntity=Permission::class, mappedBy="modul")
     */
    private $permissions;

    public function __construct()
    {
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

    public function getAplikasi(): ?Aplikasi
    {
        return $this->aplikasi;
    }

    public function setAplikasi(?Aplikasi $aplikasi): self
    {
        $this->aplikasi = $aplikasi;

        return $this;
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreateDate(): ?DateTimeImmutable
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeImmutable $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreateDateValue(): void
    {
        $this->createDate = new DateTimeImmutable();
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
            $permission->addModul($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            $permission->removeModul($this);
        }

        return $this;
    }
}

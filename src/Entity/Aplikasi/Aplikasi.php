<?php

namespace App\Entity\Aplikasi;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Aplikasi\AplikasiRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"order"={"createDate": "ASC", "nama": "ASC"}}
 * )
 * @ORM\Entity(repositoryClass=AplikasiRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="aplikasi", indexes={
 *     @ORM\Index(name="idx_aplikasi_nama_status", columns={"nama", "system_name", "status"}),
 * })
 * @UniqueEntity(fields={"nama"})
 * @UniqueEntity(fields={"systemName"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "nama": "ipartial",
 *     "systemName": "ipartial",
 *     "deskripsi": "ipartial",
 * })
 * @ApiFilter(DateFilter::class, properties={"createDate"})
 * @ApiFilter(BooleanFilter::class, properties={"status"})
 * @ApiFilter(PropertyFilter::class)
 */
class Aplikasi
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
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
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
     * @ORM\OneToMany(targetEntity=Modul::class, mappedBy="aplikasi", orphanRemoval=true)
     */
    private $moduls;

    public function __construct()
    {
        $this->moduls = new ArrayCollection();
    }

    public function __toString(): string
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
     * @return Collection|Modul[]
     */
    public function getModuls(): Collection
    {
        return $this->moduls;
    }

    public function addModul(Modul $modul): self
    {
        if (!$this->moduls->contains($modul)) {
            $this->moduls[] = $modul;
            $modul->setAplikasi($this);
        }

        return $this;
    }

    public function removeModul(Modul $modul): self
    {
        if ($this->moduls->contains($modul)) {
            $this->moduls->removeElement($modul);
            // set the owning side to null (unless already changed)
            if ($modul->getAplikasi() === $this) {
                $modul->setAplikasi(null);
            }
        }

        return $this;
    }
}

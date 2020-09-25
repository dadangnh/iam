<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Organisasi\GroupJabatanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=GroupJabatanRepository::class)
 * @ORM\Table(name="group_jabatan", indexes={
 *     @ORM\Index(name="idx_group_jabatan_nama", columns={"id", "nama"}),
 *     @ORM\Index(name="idx_group_jabatan_legacy", columns={"id", "legacy_kode"}),
 * })
 * @ApiFilter(SearchFilter::class, properties={"nama": "ipartial", "legacyKode": "iexact"})
 * @ApiFilter(PropertyFilter::class)
 */
class GroupJabatan
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
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\OneToMany(targetEntity=Jabatan::class, mappedBy="groupJabatan")
     */
    private $jabatans;

    public function __construct()
    {
        $this->jabatans = new ArrayCollection();
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

    public function getLegacyKode(): ?string
    {
        return $this->legacyKode;
    }

    public function setLegacyKode(?string $legacyKode): self
    {
        $this->legacyKode = $legacyKode;

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
            $jabatan->setGroupJabatan($this);
        }

        return $this;
    }

    public function removeJabatan(Jabatan $jabatan): self
    {
        if ($this->jabatans->contains($jabatan)) {
            $this->jabatans->removeElement($jabatan);
            // set the owning side to null (unless already changed)
            if ($jabatan->getGroupJabatan() === $this) {
                $jabatan->setGroupJabatan(null);
            }
        }

        return $this;
    }
}

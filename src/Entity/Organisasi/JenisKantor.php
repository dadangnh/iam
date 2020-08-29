<?php

namespace App\Entity\Organisasi;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Organisasi\JenisKantorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=JenisKantorRepository::class)
 */
class JenisKantor
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
    private $tipe;

    /**
     * @ORM\Column(type="integer")
     */
    private $klasifikasi;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $tanggalAktif;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $tanggalNonaktif;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $legacyId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\OneToMany(targetEntity=Kantor::class, mappedBy="jenisKantor")
     */
    private $kantors;

    public function __construct()
    {
        $this->kantors = new ArrayCollection();
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

    public function getTipe(): ?string
    {
        return $this->tipe;
    }

    public function setTipe(string $tipe): self
    {
        $this->tipe = $tipe;

        return $this;
    }

    public function getKlasifikasi(): ?int
    {
        return $this->klasifikasi;
    }

    public function setKlasifikasi(int $klasifikasi): self
    {
        $this->klasifikasi = $klasifikasi;

        return $this;
    }

    public function getTanggalAktif(): ?\DateTimeImmutable
    {
        return $this->tanggalAktif;
    }

    public function setTanggalAktif(\DateTimeImmutable $tanggalAktif): self
    {
        $this->tanggalAktif = $tanggalAktif;

        return $this;
    }

    public function getTanggalNonaktif(): ?\DateTimeImmutable
    {
        return $this->tanggalNonaktif;
    }

    public function setTanggalNonaktif(?\DateTimeImmutable $tanggalNonaktif): self
    {
        $this->tanggalNonaktif = $tanggalNonaktif;

        return $this;
    }

    public function getLegacyId(): ?int
    {
        return $this->legacyId;
    }

    public function setLegacyId(?int $legacyId): self
    {
        $this->legacyId = $legacyId;

        return $this;
    }

    public function getLegacyKode(): ?int
    {
        return $this->legacyKode;
    }

    public function setLegacyKode(?int $legacyKode): self
    {
        $this->legacyKode = $legacyKode;

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
            $kantor->setJenisKantor($this);
        }

        return $this;
    }

    public function removeKantor(Kantor $kantor): self
    {
        if ($this->kantors->contains($kantor)) {
            $this->kantors->removeElement($kantor);
            // set the owning side to null (unless already changed)
            if ($kantor->getJenisKantor() === $this) {
                $kantor->setJenisKantor(null);
            }
        }

        return $this;
    }
}

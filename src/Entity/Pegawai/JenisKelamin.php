<?php

namespace App\Entity\Pegawai;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Pegawai\JenisKelaminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=JenisKelaminRepository::class)
 * @ORM\Table(name="jenis_kelamin", indexes={
 *     @ORM\Index(name="idx_jenis_kelamin_nama", columns={"id", "nama"}),
 *     @ORM\Index(name="idx_jenis_kelamin_legacy", columns={"id", "legacy_kode"}),
 * })
 */
class JenisKelamin
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $legacyKode;

    /**
     * @ORM\OneToMany(targetEntity=Pegawai::class, mappedBy="jenisKelamin")
     */
    private $pegawais;

    public function __construct()
    {
        $this->pegawais = new ArrayCollection();
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
     * @return Collection|Pegawai[]
     */
    public function getPegawais(): Collection
    {
        return $this->pegawais;
    }

    public function addPegawai(Pegawai $pegawai): self
    {
        if (!$this->pegawais->contains($pegawai)) {
            $this->pegawais[] = $pegawai;
            $pegawai->setJenisKelamin($this);
        }

        return $this;
    }

    public function removePegawai(Pegawai $pegawai): self
    {
        if ($this->pegawais->contains($pegawai)) {
            $this->pegawais->removeElement($pegawai);
            // set the owning side to null (unless already changed)
            if ($pegawai->getJenisKelamin() === $this) {
                $pegawai->setJenisKelamin(null);
            }
        }

        return $this;
    }
}

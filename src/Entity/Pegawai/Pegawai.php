<?php

namespace App\Entity\Pegawai;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User\User;
use App\Repository\Pegawai\PegawaiRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"pegawai:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"pegawai:write"}, "swagger_definition_name"="Write"}
 * )
 * @ORM\Entity(repositoryClass=PegawaiRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="pegawai", indexes={
 *     @ORM\Index(name="idx_pegawai_data", columns={"id", "nama", "pensiun", "nik", "nip9", "nip18"}),
 *     @ORM\Index(name="idx_pegawai_legacy", columns={"id", "nip9"}),
 *     @ORM\Index(name="idx_pegawai_relation", columns={"id", "user_id", "jenis_kelamin_id", "agama_id"}),
 * })
 * @ApiFilter(BooleanFilter::class, properties={"pensiun"})
 * @ApiFilter(SearchFilter::class, properties={"nama": "ipartial", "nip9": "partial", "nip18": "partial"})
 * @ApiFilter(DateFilter::class, properties={"tanggalLahir"})
 * @ApiFilter(PropertyFilter::class)
 */
class Pegawai
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="pegawai", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $nama;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotNull()
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $tanggalLahir;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $tempatLahir;

    /**
     * @ORM\ManyToOne(targetEntity=JenisKelamin::class, inversedBy="pegawais")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $jenisKelamin;

    /**
     * @ORM\ManyToOne(targetEntity=Agama::class, inversedBy="pegawais")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $agama;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $pensiun;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $npwp;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $nik;

    /**
     * @ORM\Column(type="string", length=9, nullable=true)
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $nip9;

    /**
     * @ORM\Column(type="string", length=18, nullable=true)
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $nip18;

    /**
     * @ORM\OneToMany(targetEntity=JabatanPegawai::class, mappedBy="pegawai", orphanRemoval=true)
     * @Groups({"user:read"})
     * @Groups({"pegawai:read", "pegawai:write"})
     */
    private $jabatanPegawais;

    public function __construct()
    {
        $this->jabatanPegawais = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nama;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function getTanggalLahir(): ?DateTimeImmutable
    {
        return $this->tanggalLahir;
    }

    public function setTanggalLahir(DateTimeImmutable $tanggalLahir): self
    {
        $this->tanggalLahir = $tanggalLahir;

        return $this;
    }

    public function getTempatLahir(): ?string
    {
        return $this->tempatLahir;
    }

    public function setTempatLahir(string $tempatLahir): self
    {
        $this->tempatLahir = $tempatLahir;

        return $this;
    }

    public function getJenisKelamin(): ?JenisKelamin
    {
        return $this->jenisKelamin;
    }

    public function setJenisKelamin(?JenisKelamin $jenisKelamin): self
    {
        $this->jenisKelamin = $jenisKelamin;

        return $this;
    }

    public function getAgama(): ?Agama
    {
        return $this->agama;
    }

    public function setAgama(?Agama $agama): self
    {
        $this->agama = $agama;

        return $this;
    }

    public function getPensiun(): ?bool
    {
        return $this->pensiun;
    }

    public function setPensiun(bool $pensiun): self
    {
        $this->pensiun = $pensiun;

        return $this;
    }

    public function getNpwp(): ?string
    {
        return $this->npwp;
    }

    public function setNpwp(?string $npwp): self
    {
        $this->npwp = $npwp;

        return $this;
    }

    public function getNik(): ?string
    {
        return $this->nik;
    }

    public function setNik(?string $nik): self
    {
        $this->nik = $nik;

        return $this;
    }

    public function getNip9(): ?string
    {
        return $this->nip9;
    }

    public function setNip9(?string $nip9): self
    {
        $this->nip9 = $nip9;

        return $this;
    }

    public function getNip18(): ?string
    {
        return $this->nip18;
    }

    public function setNip18(?string $nip18): self
    {
        $this->nip18 = $nip18;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setPensiunValue(): void
    {
        $this->pensiun = false;
    }

    /**
     * @return Collection|JabatanPegawai[]
     */
    public function getJabatanPegawais(): Collection
    {
        return $this->jabatanPegawais;
    }

    public function addJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if (!$this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais[] = $jabatanPegawai;
            $jabatanPegawai->setPegawai($this);
        }

        return $this;
    }

    public function removeJabatanPegawai(JabatanPegawai $jabatanPegawai): self
    {
        if ($this->jabatanPegawais->contains($jabatanPegawai)) {
            $this->jabatanPegawais->removeElement($jabatanPegawai);
            // set the owning side to null (unless already changed)
            if ($jabatanPegawai->getPegawai() === $this) {
                $jabatanPegawai->setPegawai(null);
            }
        }

        return $this;
    }
}

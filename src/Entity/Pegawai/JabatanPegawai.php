<?php

namespace App\Entity\Pegawai;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanAtribut;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Repository\Pegawai\JabatanPegawaiRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=JabatanPegawaiRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="jabatan_pegawai", indexes={
 *     @ORM\Index(name="idx_jabatan_pegawai", columns={"id", "tanggal_mulai", "tanggal_selesai"}),
 *     @ORM\Index(name="idx_jabatan_pegawai_relation", columns={"id", "pegawai_id", "jabatan_id", "tipe_id", "kantor_id", "unit_id"}),
 * })
 */
class JabatanPegawai
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
     * @ORM\ManyToOne(targetEntity=Pegawai::class, inversedBy="jabatanPegawais")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pegawai;

    /**
     * @ORM\ManyToOne(targetEntity=Jabatan::class, inversedBy="jabatanPegawais")
     * @ORM\JoinColumn(nullable=false)
     */
    private $jabatan;

    /**
     * @ORM\ManyToOne(targetEntity=TipeJabatan::class, inversedBy="jabatanPegawais")
     */
    private $tipe;

    /**
     * @ORM\ManyToOne(targetEntity=Kantor::class, inversedBy="jabatanPegawais")
     * @ORM\JoinColumn(nullable=false)
     */
    private $kantor;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="jabatanPegawais")
     */
    private $unit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referensi;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $tanggalMulai;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $tanggalSelesai;

    /**
     * @ORM\ManyToOne(targetEntity=JabatanAtribut::class, inversedBy="jabatanPegawais")
     */
    private $atribut;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPegawai(): ?Pegawai
    {
        return $this->pegawai;
    }

    public function setPegawai(?Pegawai $pegawai): self
    {
        $this->pegawai = $pegawai;

        return $this;
    }

    public function getJabatan(): ?Jabatan
    {
        return $this->jabatan;
    }

    public function setJabatan(?Jabatan $jabatan): self
    {
        $this->jabatan = $jabatan;

        return $this;
    }

    public function getTipe(): ?TipeJabatan
    {
        return $this->tipe;
    }

    public function setTipe(?TipeJabatan $tipe): self
    {
        $this->tipe = $tipe;

        return $this;
    }

    public function getKantor(): ?Kantor
    {
        return $this->kantor;
    }

    public function setKantor(?Kantor $kantor): self
    {
        $this->kantor = $kantor;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getReferensi(): ?string
    {
        return $this->referensi;
    }

    public function setReferensi(?string $referensi): self
    {
        $this->referensi = $referensi;

        return $this;
    }

    public function getTanggalMulai(): ?DateTimeImmutable
    {
        return $this->tanggalMulai;
    }

    public function setTanggalMulai(DateTimeImmutable $tanggalMulai): self
    {
        $this->tanggalMulai = $tanggalMulai;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setTanggalMulaiValue(): void
    {
        $this->tanggalMulai = new DateTimeImmutable();
    }

    public function getTanggalSelesai(): ?DateTimeImmutable
    {
        return $this->tanggalSelesai;
    }

    public function setTanggalSelesai(?DateTimeImmutable $tanggalSelesai): self
    {
        $this->tanggalSelesai = $tanggalSelesai;

        return $this;
    }

    public function getAtribut(): ?JabatanAtribut
    {
        return $this->atribut;
    }

    public function setAtribut(?JabatanAtribut $atribut): self
    {
        $this->atribut = $atribut;

        return $this;
    }
}

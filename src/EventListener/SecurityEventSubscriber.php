<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JabatanLuar;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\KantorLuar;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Entity\Organisasi\UnitLuar;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\JabatanPegawaiLuar;
use App\Entity\User\User;
use App\Helper\RoleHelper;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SecurityEventSubscriber implements EventSubscriberInterface
{
    /** @var IriConverterInterface */
    private IriConverterInterface $iriConverter;

    private EntityManagerInterface $entityManager;

    public function __construct(IriConverterInterface $iriConverter, EntityManagerInterface $entityManager)
    {
        $this->iriConverter = $iriConverter;
        $this->entityManager = $entityManager;
    }

    #[ArrayShape([Events::JWT_CREATED => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onJwtCreated',
        ];
    }

    /**
     * @param JWTCreatedEvent $event
     * @throws Exception
     */
    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        // define pegawai data
        $jabatanPegawais = [];
        $jabatanPegawaiLuars = [];
        // Check whether user have jabatan pegawai
        if (null !== $user->getPegawai() && null !== $user->getPegawai()->getJabatanPegawais()) {
            /** @var JabatanPegawai $jabatanPegawai */
            foreach ($user->getPegawai()->getJabatanPegawais() as $jabatanPegawai) {
                // Only add jabatan pegawai that is active and not expired
                if ($jabatanPegawai->getTanggalMulai() <= new DateTimeImmutable('now')
                    && ($jabatanPegawai->getTanggalSelesai() >= new DateTimeImmutable('now')
                        || null === $jabatanPegawai->getTanggalSelesai())
                ) {
                    /** @var Jabatan $jabatan */
                    $jabatan = $jabatanPegawai->getJabatan();
                    /** @var Kantor $kantor */
                    $kantor = $jabatanPegawai->getKantor();
                    /** @var Unit $unit */
                    $unit = $jabatanPegawai->getUnit();
                    /** @var TipeJabatan $tipe */
                    $tipe = $jabatanPegawai->getTipe();

                    // Get the name from related entity
                    $namaJabatan = $jabatan?->getNama();
                    $namaKantor = $kantor?->getNama();
                    $namaUnit = $unit?->getNama();
                    $namaTipe = $tipe?->getNama();
                    $legacyKodeKpp = $kantor?->getLegacyKodeKpp();
                    $legacyKodeKanwil = $kantor?->getLegacyKodeKanwil();
                    $unitId = $unit?->getId();
                    $kantorId = $kantor?->getId();

                    // Assign to jabatanPegawais array
                    $jabatanPegawais[] = [
                        'jabatanPegawai_iri' => $this->iriConverter->getIriFromResource($jabatanPegawai),
                        'jabatan_name' => $namaJabatan,
                        'kantor_name' => $namaKantor,
                        'unit_name' => $namaUnit,
                        'tipeJabatan_name' => $namaTipe,
                        'legacyKodeKpp' => $legacyKodeKpp,
                        'legacyKodeKanwil' => $legacyKodeKanwil,
                        'kantorId' => $kantorId,
                        'unitId' => $unitId,
                        'roles' => RoleHelper::getPlainRolesNameFromJabatanPegawai(
                            $this->entityManager,
                            $jabatanPegawai
                        ),
                    ];
                }
            }
        } elseif (null !== $user->getPegawaiLuar() && null !== $user->getPegawaiLuar()->getJabatanPegawaiLuars()){
            /** @var JabatanPegawaiLuar $jabatanPegawaiLuar */
            foreach ($user->getPegawaiLuar()->getJabatanPegawaiLuars() as $jabatanPegawaiLuar) {
                // Only add jabatan pegawai that is active and not expired
                if ($jabatanPegawaiLuar->getTanggalMulai() <= new DateTimeImmutable('now')
                    && ($jabatanPegawaiLuar->getTanggalSelesai() >= new DateTimeImmutable('now')
                        || null === $jabatanPegawaiLuar->getTanggalSelesai())
                ) {
                    /** @var JabatanLuar $jabatanLuar */
                    $jabatanLuar = $jabatanPegawaiLuar->getJabatanLuar();
                    /** @var KantorLuar $kantorLuar */
                    $kantorLuar = $jabatanPegawaiLuar->getKantorLuar();
                    /** @var UnitLuar $unitLuar */
                    $unitLuar = $jabatanPegawaiLuar->getUnitLuar();
                    /** @var TipeJabatan $tipe */
                    $tipe = $jabatanPegawaiLuar->getTipe();

                    // Get the name from related entity
                    $namaJabatanLuar = $jabatanLuar?->getNama();
                    $namaKantorLuar = $kantorLuar?->getNama();
                    $namaUnitLuar = $unitLuar?->getNama();
                    $namaTipe = $tipe?->getNama();
                    $legacyKodeKpp = $kantorLuar?->getLegacyKodeKpp();
                    $legacyKodeKanwil = $kantorLuar?->getLegacyKodeKanwil();
                    $unitLuarId = $unitLuar?->getId();
                    $kantorLuarId = $kantorLuar?->getId();

                    // Assign to jabatanPegawais array
                    $jabatanPegawaiLuars[] = [
                        'jabatanPegawai_iri' => $this->iriConverter->getIriFromResource($jabatanPegawaiLuar),
                        'jabatan_name' => $namaJabatanLuar,
                        'kantor_name' => $namaKantorLuar,
                        'unit_name' => $namaUnitLuar,
                        'tipeJabatan_name' => $namaTipe,
                        'legacyKodeKpp' => $legacyKodeKpp,
                        'legacyKodeKanwil' => $legacyKodeKanwil,
                        'kantorId' => $kantorLuarId,
                        'unitId' => $unitLuarId,
                        'roles' => RoleHelper::getPlainRolesNameFromJabatanPegawaiLuar(
                            $this->entityManager,
                            $jabatanPegawaiLuar
                        ),
                    ];
                }
            }
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['roles'] = $user->getCustomRoles($this->entityManager);
        $payload['username'] = $user->getUserIdentifier();
        $payload['exp'] = (new DateTimeImmutable())->getTimestamp() + 3600;
        $payload['expired'] = (new DateTimeImmutable())->getTimestamp() + 3600;
        $payload['pegawai'] = null !== $user->getPegawai()
            ? [
                'pegawaiId' => $user->getPegawai()->getId(),
                'nama' => $user->getPegawai()->getNama(),
                'nip9' => $user->getPegawai()->getNip9(),
                'nip18' => $user->getPegawai()->getNip18(),
                'pensiun' => $user->getPegawai()->getPensiun(),
                'pangkat' => $user->getPegawai()->getPangkat(),
                'onLeave' => $user->getPegawai()->getOnLeave(),
                'jabatanPegawais' => $jabatanPegawais
            ] : null;
        $payload['pegawaiLuar'] = null !== $user->getPegawaiLuar()
            ? [
                'pegawaiId' => $user->getPegawaiLuar()->getId(),
                'nama' => $user->getPegawaiLuar()->getNama(),
                'nip18' => $user->getPegawaiLuar()->getNip18(),
                'pensiun' => $user->getPegawaiLuar()->getPensiun(),
                'pangkat' => $user->getPegawaiLuar()->getPangkat(),
                'jabatanPegawais' => $jabatanPegawaiLuars
            ] : null;

        $event->setData($payload);
    }
}

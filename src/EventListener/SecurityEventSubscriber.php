<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\User\User;
use App\Helper\RoleHelper;
use DateTimeImmutable;
use JetBrains\PhpStorm\ArrayShape;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SecurityEventSubscriber implements EventSubscriberInterface
{
    /** @var IriConverterInterface  */
    private IriConverterInterface $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
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
     */
    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        // define pegawai data
        $jabatanPegawais = [];
        // Check whether user have jabatan pegawai
        if (null !== $user->getPegawai() && null !== $user->getPegawai()->getJabatanPegawais()) {
            /** @var JabatanPegawai $jabatanPegawai */
            foreach ($user->getPegawai()->getJabatanPegawais() as $jabatanPegawai) {
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

                // Assign to jabatanPegawais array
                $jabatanPegawais[] = [
                    'jabatanPegawai_iri' => $this->iriConverter->getIriFromItem($jabatanPegawai),
                    'jabatan_name' => $namaJabatan,
                    'kantor_name' => $namaKantor,
                    'unit_name' => $namaUnit,
                    'tipeJabatan_name' => $namaTipe,
                    'roles' => RoleHelper::getPlainRolesNameFromJabatanPegawai($jabatanPegawai),
                ];
            }
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['roles'] = $user->getRoles();
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
                'jabatanPegawais' => $jabatanPegawais
            ] : null;

        $event->setData($payload);
    }
}

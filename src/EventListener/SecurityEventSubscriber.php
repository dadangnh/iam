<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\TipeJabatan;
use App\Entity\Organisasi\Unit;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\User\User;
use App\utils\RoleUtils;
use DateTimeImmutable;
use JetBrains\PhpStorm\ArrayShape;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SecurityEventSubscriber implements EventSubscriberInterface
{
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

                $namaJabatan = $namaKantor = $namaUnit = $namaTipe = null;
                if (null !== $jabatan) {
                    $namaJabatan = $jabatan->getNama();
                }

                if (null !== $kantor) {
                    $namaKantor = $kantor->getNama();
                }

                if (null !== $unit) {
                    $namaUnit = $unit->getNama();
                }

                if (null !== $tipe) {
                    $namaTipe = $tipe->getNama();
                }

                $jabatanPegawais[] = [
                    'id_jabatan_pegawai' => $jabatanPegawai->getId(),
                    'nama_jabatan' => $namaJabatan,
                    'nama_kantor' => $namaKantor,
                    'nama_unit' => $namaUnit,
                    'nama_tipe_jabatan' => $namaTipe,
                    'roles' => RoleUtils::getPlainRolesNameFromJabatanPegawai($jabatanPegawai)
                ];
            }
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['roles'] = $user->getRoles();
        $payload['username'] = $user->getUsername();
        $payload['expired'] = (new DateTimeImmutable())->getTimestamp() + 3600;
        $payload['pegawai'] = null !== $user->getPegawai()
            ? [
                'id' => $user->getPegawai()->getId(),
                'nama' => $user->getPegawai()->getNama(),
                'nip9' => $user->getPegawai()->getNip9(),
                'nip18' => $user->getPegawai()->getNip18(),
                'pensiun' => $user->getPegawai()->getPensiun(),
                'jabatan_pegawais' => $jabatanPegawais
            ] : null;

        $event->setData($payload);
    }
}

<?php


namespace App\utils;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Aplikasi\Aplikasi;
use JetBrains\PhpStorm\ArrayShape;

class AplikasiUtils
{

    /**
     * @param Aplikasi $aplikasi
     * @param IriConverterInterface $iriConverter
     * @return array
     */
    #[ArrayShape([
        'iri' => "string",
        'id' => "",
        'nama' => "null|string",
        'deskripsi' => "null|string",
        'hostname' => "null|string",
        'url' => "null|string"
    ])]
    public static function createReadableAplikasiJsonData(Aplikasi $aplikasi, IriConverterInterface $iriConverter): array
    {
        return [
            'iri' => $iriConverter->getIriFromItem($aplikasi),
            'id' => $aplikasi->getId(),
            'nama' => $aplikasi->getNama(),
            'deskripsi' => $aplikasi->getDeskripsi(),
            'hostname' => $aplikasi->getHostName(),
            'url' => $aplikasi->getUrl()
        ];
    }
}

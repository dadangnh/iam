<?php


namespace App\Helper;


use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Aplikasi\Aplikasi;
use JetBrains\PhpStorm\ArrayShape;

class AplikasiHelper
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
        'url' => "null|string",
        'status' => "bool"
    ])]
    public static function createReadableAplikasiJsonData(Aplikasi $aplikasi, IriConverterInterface $iriConverter): array
    {
        return [
            'iri' => $iriConverter->getIriFromResource($aplikasi),
            'id' => $aplikasi->getId(),
            'nama' => $aplikasi->getNama(),
            'deskripsi' => $aplikasi->getDeskripsi(),
            'hostname' => $aplikasi->getHostName(),
            'url' => $aplikasi->getUrl(),
            'status' => $aplikasi->getStatus(),
        ];
    }
}

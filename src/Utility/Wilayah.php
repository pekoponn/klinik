<?php

declare(strict_types=1);

namespace App\Utility;

/**
 * Data wilayah Indonesia (provinsi, kabupaten/kota, kecamatan) dari config/wilayah.php.
 * Dipakai oleh KrsHelper (template) dan controller (endpoint dropdown bertingkat).
 */
class Wilayah
{
    private static ?array $data = null;

    private static function data(): array
    {
        if (self::$data === null) {
            self::$data = require CONFIG . 'wilayah.php';
        }

        return self::$data;
    }

    private static function toList(array $items, string $idKey): array
    {
        $list = [];
        foreach ($items as $id => $nama) {
            $list[] = [$idKey => (string)$id, 'nama' => $nama];
        }
        usort($list, static fn($a, $b) => strcasecmp($a['nama'], $b['nama']));

        return $list;
    }

    /** @return array<int, array{id_prov: string, nama: string}> */
    public static function provinsi(): array
    {
        return self::toList(self::data()['provinsi'], 'id_prov');
    }

    /** @return array<int, array{id_kab: string, nama: string}> */
    public static function kabupaten(?string $idProv): array
    {
        return self::toList(self::data()['kabupaten'][(string)$idProv] ?? [], 'id_kab');
    }

    /** @return array<int, array{id_kec: string, nama: string}> */
    public static function kecamatan(?string $idKab): array
    {
        return self::toList(self::data()['kecamatan'][(string)$idKab] ?? [], 'id_kec');
    }

    /** Nama wilayah dari kode (2/4/6 digit). Mengembalikan null bila tidak ditemukan. */
    public static function nama(?string $kode): ?string
    {
        $kode = (string)$kode;
        $d = self::data();
        $len = strlen($kode);
        if ($len === 2) {
            return $d['provinsi'][$kode] ?? null;
        }
        if ($len === 4) {
            return $d['kabupaten'][substr($kode, 0, 2)][$kode] ?? null;
        }
        if ($len === 6) {
            return $d['kecamatan'][substr($kode, 0, 4)][$kode] ?? null;
        }

        return null;
    }
}

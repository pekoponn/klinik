<?php
declare(strict_types=1);

namespace App\Utility;

use Cake\Core\Configure;
use RuntimeException;

class DataCipher
{
    private const PREFIX = 'enc:v1:';
    private const CIPHER = 'aes-256-gcm';

    private static ?string $key = null;

    private static function key(): string
    {
        if (self::$key === null) {
            $raw = (string)Configure::read('DataCipher.key');
            $key = base64_decode($raw, true);
            if ($key === false || strlen($key) !== 32) {
                throw new RuntimeException('DataCipher.key belum diatur atau tidak valid (harus base64 dari 32 byte).');
            }
            self::$key = $key;
        }

        return self::$key;
    }

    public static function encrypt(?string $plain): ?string
    {
        if ($plain === null || $plain === '' || str_starts_with($plain, self::PREFIX)) {
            return $plain;
        }

        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plain, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) {
            throw new RuntimeException('Enkripsi gagal.');
        }

        return self::PREFIX . base64_encode($iv . $tag . $cipher);
    }

    /**
     * Data lama yang belum terenkripsi (tanpa prefix) dikembalikan apa adanya,
     * supaya aplikasi tetap jalan selama masa migrasi.
     */
    public static function decrypt(?string $value): ?string
    {
        if ($value === null || $value === '' || !str_starts_with($value, self::PREFIX)) {
            return $value;
        }

        $raw = base64_decode(substr($value, strlen(self::PREFIX)), true);
        if ($raw === false || strlen($raw) < 28) {
            throw new RuntimeException('Data terenkripsi rusak.');
        }

        $iv = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $cipher = substr($raw, 28);
        $plain = openssl_decrypt($cipher, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($plain === false) {
            throw new RuntimeException('Dekripsi gagal (kunci salah atau data berubah).');
        }

        return $plain;
    }

    /**
     * Hash berkunci (HMAC) untuk pencarian persis. Bukan SHA-256 polos,
     * supaya nomor tidak bisa ditebak lewat brute force.
     */
    public static function hash(?string $plain): ?string
    {
        if ($plain === null || $plain === '') {
            return null;
        }
        $hashKey = hash_hmac('sha256', 'hash-key', self::key(), true);

        return hash_hmac('sha256', trim($plain), $hashKey);
    }
}
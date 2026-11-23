<?php
declare(strict_types=1);

namespace App\Database\Type;

use App\Utility\DataCipher;
use Cake\Database\Driver;
use Cake\Database\Type\BaseType;

class EncryptedStringType extends BaseType
{
    public function toPHP(mixed $value, Driver $driver): mixed
    {
        return $value === null ? null : DataCipher::decrypt((string)$value);
    }

    public function toDatabase(mixed $value, Driver $driver): mixed
    {
        return $value === null ? null : DataCipher::encrypt((string)$value);
    }

    public function marshal(mixed $value): mixed
    {
        return $value === null ? null : (string)$value;
    }
}
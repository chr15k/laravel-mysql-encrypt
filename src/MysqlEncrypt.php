<?php

namespace Chr15k\MysqlEncrypt;

use Chr15k\MysqlEncrypt\Exceptions\MissingAesKeyException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Expression;

class MysqlEncrypt
{
    public static function encrypt(mixed $value): Expression
    {
        self::ensureAesKeyIsSet();

        return DB::raw(sprintf(
            "AES_ENCRYPT('%s', UNHEX('%s'))",
            $value, config('mysql-encrypt.key')
        ));
    }

    public static function decrypt(mixed $column): Expression
    {
        self::ensureAesKeyIsSet();

        return DB::raw(sprintf(
            "CAST(AES_DECRYPT(%s, UNHEX('%s')) as CHAR) AS '%s'",
            $column, config('mysql-encrypt.key'), $column
        ));
    }

    public static function decryptString(string $column, string $value, string $operator = 'LIKE'): string
    {
        self::ensureAesKeyIsSet();

        return sprintf(
            "CAST(AES_DECRYPT(%s, UNHEX('%s')) as CHAR) %s '%s'",
            $column, config('mysql-encrypt.key'), $operator, $value
        );
    }

    protected static function ensureAesKeyIsSet()
    {
        if (empty(config('mysql-encrypt.key'))) {
            throw new MissingAesKeyException();
        }
    }
}




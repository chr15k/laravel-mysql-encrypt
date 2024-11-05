<?php

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

if (! function_exists('db_encrypt')) {
    function db_encrypt(mixed $value): Expression
    {
        return DB::raw(sprintf(
            "AES_ENCRYPT('%s', UNHEX('%s'))",
            $value, config('mysql-encrypt.key')
        ));
    }
}

if (! function_exists('db_decrypt')) {
    function db_decrypt(mixed $column): Expression
    {
        return DB::raw(sprintf(
            "CAST(AES_DECRYPT(%s, UNHEX('%s')) as CHAR) AS '%s'",
            $column, config('mysql-encrypt.key'), $column
        ));
    }
}

if (! function_exists('db_decrypt_string')) {
    function db_decrypt_string(string $column, string $value, string $operator = 'LIKE'): string
    {
        return sprintf(
            "CAST(AES_DECRYPT(%s, UNHEX('%s')) as CHAR) %s '%s'",
            $column, config('mysql-encrypt.key'), $operator, $value
        );
    }
}

<?php

use Chr15k\MysqlEncrypt\MysqlEncrypt;
use Illuminate\Database\Query\Expression;

if (! function_exists('db_encrypt')) {
    function db_encrypt(mixed $value): Expression
    {
        return MysqlEncrypt::encrypt($value);
    }
}

if (! function_exists('db_decrypt')) {
    function db_decrypt(mixed $column): Expression
    {
        return MysqlEncrypt::decrypt($column);
    }
}

if (! function_exists('db_decrypt_string')) {
    function db_decrypt_string(string $column, string $value, string $operator = 'LIKE'): string
    {
        return MysqlEncrypt::decryptString($column, $value, $operator);
    }
}

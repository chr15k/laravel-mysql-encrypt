<?php

namespace Chr15k\MysqlEncrypt\Exceptions;

use Exception;

class MissingAesKeyException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Missing AES key. Please add APP_AESENCRYPT_KEY to your .env or add to config.'
        );
    }
}
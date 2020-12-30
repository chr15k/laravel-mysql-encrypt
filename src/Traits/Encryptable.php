<?php

namespace Chr15k\MysqlEncrypt\Traits;

use Illuminate\Support\Facades\DB;
use Chr15k\MysqlEncrypt\Scopes\DecryptSelectScope;

trait Encryptable
{
    /**
     * @return void
     */
    public static function bootEncryptable()
    {
        static::addGlobalScope(new DecryptSelectScope);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (is_null($value) || !in_array($key, $this->encryptable)) {
            return parent::setAttribute($key, $value);
        }

        return parent::setAttribute($key, db_encrypt($value));
    }

    /**
     * @return array
     */
    public function encryptable(): array
    {
        return $this->encryptable ?? [];
    }
}

<?php

namespace Chr15k\MysqlEncrypt\Tests\Models;

use Chr15k\MysqlEncrypt\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Encryptable;

    protected $fillable = ['name', 'email'];

    protected $encryptable = ['name'];
}
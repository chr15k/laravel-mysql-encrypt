<?php

namespace Chr15k\MysqlEncrypt\Tests;

use Illuminate\Support\Facades\DB;
use Chr15k\MysqlEncrypt\MysqlEncrypt;
use Illuminate\Database\Query\Expression;
use Chr15k\MysqlEncrypt\Tests\Models\User;
use Illuminate\Database\Query\Grammars\Grammar;
use Chr15k\MysqlEncrypt\Exceptions\MissingAesKeyException;

class MysqlEncryptTest extends TestCase
{
    public function testEncryptMethod()
    {
        $value = 'Sensitive Data';
        $expression = new Expression($value);

        DB::shouldReceive('raw')
            ->once()
            ->andReturn($expression);

        $encrypted = MysqlEncrypt::encrypt($value);

        $this->assertEquals($expression, $encrypted);
        $this->assertEquals($expression->getValue(new Grammar), $value);
    }

    public function testDecryptMethod()
    {
        $value = 'Sensitive Data';
        $expression = new Expression($value);

        DB::shouldReceive('raw')
            ->once()
            ->andReturn($expression);

        $encrypted = MysqlEncrypt::decrypt('name');

        $this->assertEquals($expression, $encrypted);
        $this->assertEquals($expression->getValue(new Grammar), $value);
    }

    public function testDecryptStringMethod()
    {
        $encrypted = MysqlEncrypt::decryptString('name', 'Sensitive Data');
        $key = config('mysql-encrypt.key');

        $this->assertSame("CAST(AES_DECRYPT(name, UNHEX('{$key}')) as CHAR) LIKE 'Sensitive Data'", $encrypted);
    }

    public function testModelHasExpressionWhenAccessingEncryptableAttribute()
    {
        $user = new User;
        $user->name = 'Chris';

        $key = config('mysql-encrypt.key');

        $this->assertEquals(new Expression("AES_ENCRYPT('Chris', UNHEX('{$key}'))"), $user->name);
    }

    public function testModelHasValueWhenAccessingNonEncryptableAttribute()
    {
        $user = new User;
        $email = 'chris@example.com';
        $user->email = $email;

        $this->assertSame($email, $user->email);
    }

    public function testThrowsExceptionIfMissingAesKeyWhenCallingEncrypt()
    {
        config()->set('mysql-encrypt.key', '');

        $this->expectException(MissingAesKeyException::class);

        MysqlEncrypt::encrypt('Sensitive Data');
    }

    public function testThrowsExceptionIfMissingAesKeyWhenCallingDecrypt()
    {
        config()->set('mysql-encrypt.key', '');

        $this->expectException(MissingAesKeyException::class);

        MysqlEncrypt::decrypt('name');
    }

    public function testThrowsExceptionIfMissingAesKeyWhenCallingDecryptString()
    {
        config()->set('mysql-encrypt.key', '');

        $this->expectException(MissingAesKeyException::class);

        MysqlEncrypt::decryptString('name', 'Sensitive Data');
    }
}
<?php

declare(strict_types=1);

namespace Chr15k\MysqlEncrypt\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Chr15k\MysqlEncrypt\Providers\MysqlEncryptServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [MysqlEncryptServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mysql-encrypt.key', '5b722b307fce6c944905d132691d5e4a2214b7fe92b738920eb3fce3a90420a19511c3010a0e7712b054daef5b57bad59ecbd93b3280f210578f547f4aed4d25');
    }
}

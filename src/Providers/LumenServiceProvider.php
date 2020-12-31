<?php

namespace Chr15k\MysqlEncrypt\Providers;

use Illuminate\Support\ServiceProvider;
use Chr15k\MysqlEncrypt\Traits\ValidatesEncrypted;

class LumenServiceProvider extends ServiceProvider
{
    use ValidatesEncrypted;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->configure('mysql-encrypt');

        $path = realpath(__DIR__.'/../../config/config.php');

        $this->mergeConfigFrom($path, 'mysql-encrypt');

        $this->addValidators();
    }
}

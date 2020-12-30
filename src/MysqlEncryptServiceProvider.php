<?php

namespace Chr15k\MysqlEncrypt;

use Illuminate\Support\ServiceProvider;

class MysqlEncryptServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('mysql-encrypt.php'),
        ], 'config');
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'mysql-encrypt');
    }
}

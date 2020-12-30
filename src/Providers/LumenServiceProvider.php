<?php

namespace Chr15k\MysqlEncrypt\Providers;

use Illuminate\Support\ServiceProvider;

class LumenServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->configure('mysql-encrypt');

        $path = realpath(__DIR__.'/../../config/config.php');

        $this->mergeConfigFrom($path, 'mysql-encrypt');
    }
}

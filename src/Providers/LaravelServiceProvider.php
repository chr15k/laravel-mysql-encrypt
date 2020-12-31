<?php

namespace Chr15k\MysqlEncrypt\Providers;

use PDOException;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Chr15k\MysqlEncrypt\Traits\ValidatesEncrypted;

class LaravelServiceProvider extends ServiceProvider
{
    use ValidatesEncrypted;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('mysql-encrypt.php'),
        ], 'config');

        $this->addValidators();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'mysql-encrypt');
    }
}

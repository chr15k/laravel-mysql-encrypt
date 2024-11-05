<?php

namespace Chr15k\MysqlEncrypt\Providers;

use Chr15k\MysqlEncrypt\Commands\AesKeyGenerateCommand;
use Illuminate\Support\ServiceProvider;

class MysqlEncryptServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('mysql-encrypt.php'),
        ], 'config');

        $this->registerCommands();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'mysql-encrypt');
    }

    protected function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            AesKeyGenerateCommand::class,
        ]);
    }
}

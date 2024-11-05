<?php

namespace Chr15k\MysqlEncrypt\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AesKeyGenerateCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'laravel-mysql-encrypt:key:generate';

    protected $description = 'Generate secure AES Key hash';

    /** @see https://dev.mysql.com/doc/refman/8.0/en/encryption-functions.html#function_aes-decrypt */
    const HASHING_ALGORITHM = 'sha512';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        do {
            $passphrase = $this->secret('Passphrase?');

            $validator = Validator::make(['passphrase' => $passphrase], [
                'passphrase' => 'required|min:3|max:255',
            ]);

            if ($validator->fails()) {
                $this->error($validator->errors()->first());
            }
        } while ($validator->fails());

        $key = hash(self::HASHING_ALGORITHM, $passphrase);

        if (! $this->setKeyInEnvironmentFile($key)) {
            return Command::FAILURE;
        }

        $this->laravel['config']['mysql-encrypt.key'] = $key;

        $this->info('AES key set successfully.');

        return Command::SUCCESS;
    }

    /**
     * Set the application key in the environment file.
     */
    protected function setKeyInEnvironmentFile(string $key): bool
    {
        $currentKey = $this->laravel['config']['mysql-encrypt.key'];

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        if (! $this->writeNewEnvironmentFileWith($key)) {
            return false;
        }

        return true;
    }

    /**
     * Write a new environment file with the given key.
     */
    protected function writeNewEnvironmentFileWith(string $key): bool
    {
        $replaced = preg_replace(
            $this->keyReplacementPattern(),
            'APP_AESENCRYPT_KEY='.$key,
            $input = file_get_contents($this->laravel->environmentFilePath())
        );

        $exists = Str::contains($replaced, 'APP_AESENCRYPT_KEY=');
        $error = 'Unable to set application key.';

        if ($exists && $input === $replaced) {
            $this->comment($error.' No change detected for APP_AESENCRYPT_KEY in the .env file, skipping...');

            return false;
        }

        if (is_null($replaced) || ! $exists) {
            $this->error($error.' No APP_AESENCRYPT_KEY variable was found in the .env file.');

            return false;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        return true;
    }

    /**
     * Get a regex pattern that will match env APP_AESENCRYPT_KEY with any random key.
     */
    protected function keyReplacementPattern(): string
    {
        return sprintf(
            '/^APP_AESENCRYPT_KEY%s/m',
            preg_quote('='.$this->laravel['config']['mysql-encrypt.key'], '/')
        );
    }
}

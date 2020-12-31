# Laravel MySql AES Encrypt/Decrypt

[![Latest Stable Version](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/v)](//packagist.org/packages/chr15k/laravel-mysql-encrypt) [![Latest Unstable Version](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/v/unstable)](//packagist.org/packages/chr15k/laravel-mysql-encrypt) [![Total Downloads](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/downloads)](//packagist.org/packages/chr15k/laravel-mysql-encrypt) [![License](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/license)](//packagist.org/packages/chr15k/laravel-mysql-encrypt)

Laravel/Lumen database encryption at database side using native AES_DECRYPT and AES_ENCRYPT functions.
Automatically encrypt and decrypt fields in your Models.

## Install
### 1. Composer
```bash
composer require chr15k/laravel-mysql-encrypt
```

### 2. Publish config (optional)
```bash
php artisan vendor:publish --provider="Chr15k\MysqlEncrypt\MysqlEncryptServiceProvider"
```

### 3. Configure Provider
- For Laravel 5.5 or later, the service provider is automatically loaded, skip this step.

- For Laravel 5.4 or earlier, add the following to `config/app.php`:
```php
'providers' => array(
    Chr15k\\MysqlEncrypt\\MysqlEncryptServiceProvider::class
);
```

- For Lumen, add the following to `bootstrap/app.php`:
```php
$app->register(Chr15k\MysqlEncrypt\Providers\LumenServiceProvider::class);
```

### 4. Set encryption key in `.env` file
```
APP_AESENCRYPT_KEY=yourencryptionkey
```

## Update Models
```php
<?php

namespace App;

use Chr15k\MysqlEncrypt\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Encryptable; // <-- 1. Include trait

    protected $encryptable = [ // <-- 2. Include columns to be encrypted
        'email',
        'first_name',
        'last_name',
        'telephone',
    ];
}
```

## Validators
`unique_encrypted`
```
unique_encrypted:<table>,<field(optional)>
```

`exists_encrypted`
```
exists_encrypted:<table>,<field(optional)>
```

## Scopes
Custom Local scopes available:

`whereEncrypted`
`whereNotEncrypted`
`orWhereEncrypted`
`orWhereNotEncrypted`
`orderByEncrypted`

Global scope `DecryptSelectScope` automatically booted in models using `Encryptable` trait.

## Schema columns to support encrypted data
```php
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});

// Once the table has been created, use ALTER TABLE to create VARBINARY
// or BLOB types to store encrypted data.
DB::statement('ALTER TABLE `users` ADD `first_name` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `last_name` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `email` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `telephone` VARBINARY(50)');
```

## License
The MIT License (MIT). Please see [License File](https://github.com/chr15k/laravel-mysql-encrypt/blob/master/LICENSE) for more information.

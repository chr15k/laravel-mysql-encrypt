# Laravel MySql AES Encrypt/Decrypt

[![Latest Stable Version](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/v)](//packagist.org/packages/chr15k/laravel-mysql-encrypt) [![Latest Unstable Version](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/v/unstable)](//packagist.org/packages/chr15k/laravel-mysql-encrypt) [![Total Downloads](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/downloads)](//packagist.org/packages/chr15k/laravel-mysql-encrypt) [![License](https://poser.pugx.org/chr15k/laravel-mysql-encrypt/license)](//packagist.org/packages/chr15k/laravel-mysql-encrypt)

Laravel MySQL encryption using native MySQL AES_DECRYPT and AES_ENCRYPT functions.
Automatically encrypt and decrypt fields in your Models.

## Install

### 1. Composer

```bash
composer require chr15k/laravel-mysql-encrypt
```

### 2. Publish config

```bash
php artisan vendor:publish --provider="Chr15k\MysqlEncrypt\Providers\MysqlEncryptServiceProvider"
```

### 3. AES Key generation

Add to .env file:

```
APP_AESENCRYPT_KEY=
```

Generate a new secure AES key (or add your existing key manually):

```bash
php artisan laravel-mysql-encrypt:key:generate
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

### # UniqueEncrypted(table, field(optional), ignore_id(optional))

```php
<?php

use Chr15k\MysqlEncrypt\Rules\UniqueEncrypted;

Validator::make($data, [
    'name' => [new UniqueEncrypted('users')],
]);
```

### # ExistsEncrypted(table,field(optional))

```php
<?php

use Chr15k\MysqlEncrypt\Rules\ExistsEncrypted;

Validator::make($data, [
    'name' => [new ExistsEncrypted('users')],
]);
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

## Example Usage

```php
<?php

use App\Models\User;
use Chr15k\MysqlEncrypt\Rules\ExistsEncrypted;
use Chr15k\MysqlEncrypt\Rules\UniqueEncrypted;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

Route::get('/', function () {

    $user = User::firstOrCreate([
        'email' => 'test@example.com',
    ], [
        'name' => 'Test',
        'email' => 'test@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'remember_token' => Str::random(10),
    ]);

    // querying an encrypted value using the base methods will not work (as expected):
    dump(User::where('name', 'Test')->first()); // => null

    // querying through the encrypted scopes will decrypt the value as expected:
    dump(
        User::whereEncrypted('name', 'Test')
            ->orWhereEncrypted('name', 'Chris')
            ->first()
    ); // => App\Models\User

    // Accessing the encrypted attribute on the model will automatically decrypt the value:
    dump($user->name); // => 'Test'


    // Validation rules

    // ExistsEncrypted
    $validator = Validator::make(['name' => 'Chris'], [
        'name' => [new ExistsEncrypted('users')],
    ]);
    if ($validator->fails()) {
        dump($validator->errors()->first()); // => "The selected name does not exist"
    }

    // UniqueEncrypted
    $validator = Validator::make(['name' => 'Test'], [
        'name' => [new UniqueEncrypted('users')],
    ]);
    if ($validator->fails()) {
        dump($validator->errors()->first()); // => "The name field must be unique"
    }

});
```

## License

The MIT License (MIT). Please see [License File](https://github.com/chr15k/laravel-mysql-encrypt/blob/master/LICENSE) for more information.

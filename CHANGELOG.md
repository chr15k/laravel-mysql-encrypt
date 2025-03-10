## 2.2.1 - 2025-03-09

-   Support Laravel 12

## 2.1.1 - 2024-11-29

-   Remove call to global helper

## 2.1.0 - 2024-11-29

-   Minor refactor
-   Tests added
-   Missing AES key exception class added

## 2.0.4 - 2024-11-27

-   Fix model refresh issue relating to dirty encryptable attributes

## 2.0.3 - 2024-11-05

-   Update readme

## 2.0.2 - 2024-11-05

-   Remove console attribute

## 2.0.1 - 2024-11-05

-   Update readme

## 2.0.0 - 2024-11-05

-   Added new AES key generation command (using sha512 hashing alorithm as per [MySQL encrypt docs](https://dev.mysql.com/doc/refman/8.0/en/encryption-functions.html#function_aes-decrypt))
-   Refactored validators to implement ValidationRule contract
-   Automatic decryption when accessing properties on a model
-   Fixed a bug where encrypted model fields where not being hydrated with decrypted values (i.e. on creation)
-   Updated MySQL AES_DECRYPT/AES_ENCRYPT queries as per [MySQL encrypt docs](https://dev.mysql.com/doc/refman/8.0/en/encryption-functions.html#function_aes-decrypt)
-   Updated README with example usage
-   Discontinued Lumen support as the project has been archived as of Apr 9, 2024

## 1.0.0

-   Initial release

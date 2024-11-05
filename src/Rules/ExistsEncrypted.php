<?php

namespace Chr15k\MysqlEncrypt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ExistsEncrypted implements ValidationRule
{
    /**
     * Constructor method.
     */
    public function __construct(
        public string $table,
        public ?string $column = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $field = $this->column ?? $attribute;

        $items = DB::select(sprintf(
            "SELECT count(*) as aggregate FROM `%s` WHERE `%s` LIKE AES_ENCRYPT('%s', UNHEX('%s'))",
            $this->table, $field, $value, config('mysql-encrypt.key')
        ));

        if ($items[0]->aggregate === 0) {
            $fail("The selected {$field} does not exist");
        }
    }
}

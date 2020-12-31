<?php

namespace Chr15k\MysqlEncrypt\Traits;

use PDOException;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

trait ValidatesEncrypted
{
 /**
     * Validators.
     *
     * @return void
     */
    public function addValidators()
    {
        Validator::extend('unique_encrypted', function ($attribute, $value, array $parameters) {

            $this->requireParameterCount(1, $parameters, 'unique_encrypted');

            $this->requireTableExists($parameters[0]);

            $field = isset($parameters[1]) ? $parameters[1] : $attribute;
            $ignore = isset($parameters[2]) ? $parameters[2] : null;

            $items = DB::select("SELECT count(*) as aggregate FROM `".$parameters[0]."` WHERE AES_DECRYPT(`".$field."`, '".config("app.key")."') LIKE '".$value."' COLLATE utf8mb4_general_ci".($ignore ? " AND id != ".$ignore : ''));

            return $items[0]->aggregate === 0;
        });

        Validator::extend('exists_encrypted', function ($attribute, $value, array $parameters) {

            $this->requireParameterCount(1, $parameters, 'exists_encrypted');

            $this->requireTableExists($parameters[0]);

            $field = isset($parameters[1]) ? $parameters[1] : $attribute;

            $items = DB::select("SELECT count(*) as aggregate FROM `".$parameters[0]."` WHERE AES_DECRYPT(`".$field."`, '".config("app.key")."') LIKE '".$value."' COLLATE utf8mb4_general_ci");

            return $items[0]->aggregate > 0;
        });
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param  int    $count
     * @param  array  $parameters
     * @param  string $rule
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }

    /**
     * The table must exist.
     *
     * @param  string  $table
     * @return void
     *
     * @throws PDOException
     */
    public function requireTableExists($table)
    {
        if (! Schema::hasTable($table)) {
            throw new PDOException("Table $table not found.");
        }
    }
}

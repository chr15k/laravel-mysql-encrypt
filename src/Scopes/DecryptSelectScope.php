<?php

namespace Chr15k\MysqlEncrypt\Scopes;

use Chr15k\MysqlEncrypt\MysqlEncrypt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class DecryptSelectScope implements Scope
{
    public function apply(Builder $builder, Model $model): Builder
    {
        $encryptable = $model->encryptable();

        $columns = Schema::connection($model->getConnectionName())
            ->getColumnListing($model->getTable());

        if (empty($encryptable) || empty($columns)) {
            return $builder->addSelect(...$columns);
        }

        return $builder->addSelect(...collect($columns)->map(
            fn ($column) => (in_array($column, $encryptable)) ? MysqlEncrypt::decrypt($column) : $column
        ));
    }
}

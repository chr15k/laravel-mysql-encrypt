<?php

namespace Chr15k\MysqlEncrypt\Traits;

use Chr15k\MysqlEncrypt\Scopes\DecryptSelectScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;

trait Encryptable
{
    public static function bootEncryptable(): void
    {
        static::addGlobalScope(new DecryptSelectScope);

        static::saved(function ($model) {
            if ($model->hasDirtyEncrypted()) {
                $model->refreshWithGlobalScopes();
            }
        });
    }

    public function setAttribute($key, $value)
    {
        if ($this->shouldBypass($key)) {
            return parent::setAttribute($key, $value);
        }

        return parent::setAttribute($key, db_encrypt($value));
    }

    public function encryptable(): array
    {
        return $this->encryptable ?? [];
    }

    public function hasDirtyEncrypted(): bool
    {
        return (bool) $this->getDirtyEncrypted()->count();
    }

    public function getDirtyEncrypted(): Collection
    {
        return collect($this->getDirty())
            ->filter(fn ($value, $key) => in_array($key, $this->encryptable()));
    }

    public function refreshWithGlobalScopes(): self
    {
        $this->setRawAttributes(
            $this->where(
                $this->getKeyName(),
                $this->getKeyForSelectQuery()
            )
                ->useWritePdo()
                ->firstOrFail()
                ->attributes
        );

        $this->load(collect($this->relations)->reject(function ($relation) {
            return $relation instanceof Pivot
                || (is_object($relation) && in_array(AsPivot::class, class_uses_recursive($relation), true));
        })->keys()->all());

        $this->syncOriginal();

        return $this;
    }

    private function shouldBypass(string $key): bool
    {
        return ! in_array($key, $this->encryptable());
    }

    public function scopeWhereEncrypted(Builder $query, string $column, string $value): Builder
    {
        return $query->whereRaw(db_decrypt_string($column, $value));
    }

    public function scopeWhereNotEncrypted(Builder $query, string $column, string $value): Builder
    {
        return $query->whereRaw(db_decrypt_string($column, $value, 'NOT LIKE'));
    }

    public function scopeOrWhereEncrypted(Builder $query, string $column, string $value): Builder
    {
        return $query->orWhereRaw(db_decrypt_string($column, $value));
    }

    public function scopeOrWhereNotEncrypted(Builder $query, string $column, string $value): Builder
    {
        return $query->orWhereRaw(db_decrypt_string($column, $value, 'NOT LIKE'));
    }

    public function scopeOrderByEncrypted(Builder $query, string $column, mixed $direction): Builder
    {
        return $query->orderByRaw(db_decrypt_string($column, $direction, ''));
    }
}

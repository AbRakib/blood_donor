<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;

abstract class DomainModel extends Model
{
    use SoftDeletes;

    public static function bootSoftDeletes(): void
    {
        static::addGlobalScope(new class extends SoftDeletingScope
        {
            public function apply(Builder $builder, Model $model): void
            {
                parent::apply($builder, $model);
                $builder->where($model->qualifyColumn('deleted'), 0);
            }
        });
    }

    protected static function booted(): void
    {
        static::creating(function (DomainModel $model): void {
            $model->created_by = auth()->id();
        });
        static::saving(function (DomainModel $model): void {
            $model->updated_by = auth()->id();
        });
        static::restoring(function (DomainModel $model): void {
            $model->deleted = 0;
            $model->deleted_by = null;
        });

    }

    protected function runSoftDelete(): void
    {
        $time = $this->freshTimestamp();
        $this->forceFill([
            'deleted' => 1, 'deleted_at' => $time, 'deleted_by' => auth()->id(),
            'updated_at' => $time, 'updated_by' => auth()->id(),
        ]);
        $this->setKeysForSaveQuery($this->newModelQuery())->update([
            'deleted' => 1, 'deleted_at' => $this->fromDateTime($time), 'deleted_by' => $this->deleted_by,
            'updated_at' => $this->fromDateTime($time), 'updated_by' => $this->updated_by,
        ]);
        $this->syncOriginal();
        $this->fireModelEvent('trashed', false);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), 1);
    }

    protected function casts(): array
    {
        return ['status' => 'integer', 'deleted' => 'integer', 'deleted_at' => 'datetime'];
    }
}

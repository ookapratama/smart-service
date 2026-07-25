<?php

namespace App\Traits;

use App\Models\Instansi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait BelongsToInstansi
 *
 * Gunakan trait ini di Model yang datanya harus terisolasi per tenant (instansi).
 * Scoping bergantung pada binding container `currentInstansi`, yang akan diisi
 * oleh middleware resolusi tenant (subdomain) pada fase berikutnya. Selama binding
 * belum ada (console, test, super-admin platform), query tidak difilter.
 *
 * Contoh penggunaan:
 * ```php
 * class Tiket extends Model
 * {
 *     use BelongsToInstansi;
 * }
 * ```
 */
trait BelongsToInstansi
{
    public static function bootBelongsToInstansi(): void
    {
        static::addGlobalScope('instansi', function (Builder $builder) {
            if (app()->bound('currentInstansi')) {
                $builder->where(
                    $builder->getModel()->qualifyColumn('instansi_id'),
                    app('currentInstansi')->id
                );
            }
        });

        static::creating(function (Model $model) {
            if (is_null($model->instansi_id) && app()->bound('currentInstansi')) {
                $model->instansi_id = app('currentInstansi')->id;
            }
        });
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }
}

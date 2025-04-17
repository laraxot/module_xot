<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Traits;

use Illuminate\Support\Str;

/**
 * Undocumented trait
 * https://www.larashout.com/using-uuids-in-laravel-models.
 */
trait HasUuid
{
    /**
     * Boot function from Laravel.
     */
    protected static function bootHasUuid()
    {
        // parent::boot();
        static::creating(
            function ($model) {
                if (empty($model->{$model->getKeyName()})) {
                    $model->{$model->getKeyName()} = Str::uuid()->toString();
                }
            }
        );
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}

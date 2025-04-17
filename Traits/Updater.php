<?php

declare(strict_types=1);

namespace Modules\Xot\Traits;

/**
 * Trait Updater.
 * https://dev.to/hasanmn/automatically-update-createdby-and-updatedby-in-laravel-using-bootable-traits-28g9.
 */
trait Updater
{
    /**
     * Undocumented function.
     * move to modelservice.
     */
    /*

     * move to modelservice.
     */
    /*
    public function getTableColumns(): array {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    /**
     * bootUpdater function.
    */

    /**
     * bootUpdater function.
     *
     * @return void
     */
    protected static function bootUpdater()
    {
        // parent::boot();
        /*
         * During a model create Eloquent will also update the updated_at field so
         * need to have the updated_by field here as well.
         **/
        static::creating(
            function ($model) {
                if (null !== auth()->user()) {
                    $model->created_by = auth()->user()->handle ?? '';
                    $model->updated_by = auth()->user()->handle ?? '';
                }
            }
        );

        /*
         * updating.
         */
        static::updating(
            function ($model) {
                $model->updated_by = auth()->user()->handle ?? '';
            }
        );
        // -------------------------------------------------------------------------------------
        /*
         * Deleting a model is slightly different than creating or deleting.
         * For deletes we need to save the model first with the deleted_by field
        **/
        /*
        static::deleting(function ($model) {
            $model->deleted_by = \Auth::user()->handle;
            $model->save();
        });
        */
        // ----------------------
    }

    // end function boot
}// end trait Updater

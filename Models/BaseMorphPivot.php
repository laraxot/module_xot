<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Modules\Xot\Traits\Updater;

/**
 * Class BaseMorphPivot
 * @package Modules\Xot\Models
 */
abstract class BaseMorphPivot extends MorphPivot {
    use Updater;

    /**
     * @var array
     */
    protected array $appends = [];
    /**
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * @var bool
     */
    public bool $incrementing = true;
    /**
     * @var bool
     */
    public bool $timestamps = true;
    //protected $attributes = ['related_type' => 'cuisine_cat'];
    /**
     * @var string[]
     */
    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        // 'published_at',
    ];
    /**
     * @var string[]
     */
    protected array $fillable = [
        'id',
        'post_id', 'post_type',
        'related_type',
        'auth_user_id', //in amenity no, in rating si
        'note',
    ];
}

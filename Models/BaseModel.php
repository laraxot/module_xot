<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
//---------- traits
use Modules\Xot\Traits\Updater;

/**
 * Class BaseModel
 * @package Modules\Xot\Models
 */
abstract class BaseModel extends Model {
    use Updater;
    use Searchable;

    /**
     * @var string[]
     */
    protected $fillable = ['id'];
    /**
     * @var array
     */
    protected $casts = [
        //'published_at' => 'datetime:Y-m-d', // da verificare
    ];

    /**
     * @var string[]
     */
    protected $dates = ['published_at', 'created_at', 'updated_at'];
    /**
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * @var bool
     */
    public $incrementing = true;
    /**
     * @var array
     */
    protected $hidden = [
        //'password'
    ];
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images() {
        return $this->morphMany(Image::class, 'post');
    }
}

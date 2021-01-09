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
    protected array $fillable = ['id'];
    /**
     * @var array
     */
    protected array $casts = [
        //'published_at' => 'datetime:Y-m-d', // da verificare
    ];

    /**
     * @var string[]
     */
    protected array $dates = ['published_at', 'created_at', 'updated_at'];
    /**
     * @var string
     */
    protected string $primaryKey = 'id';
    /**
     * @var bool
     */
    public bool $incrementing = true;
    /**
     * @var array
     */
    protected array $hidden = [
        //'password'
    ];
    /**
     * @var bool
     */
    public bool $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images() {
        return $this->morphMany(Image::class, 'post');
    }
}

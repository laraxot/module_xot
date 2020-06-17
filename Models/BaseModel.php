<?php

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
//---------- traits
use Modules\Xot\Traits\Updater;

abstract class BaseModel extends Model {
    use Updater;
    use Searchable;

    protected $fillable = ['id'];
    protected $casts = [
        //'published_at' => 'datetime:Y-m-d', // da verificare
    ];

    protected $dates = ['published_at', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $hidden = [
        //'password'
    ];
    public $timestamps = true;

    public function images() {
        return $this->morphMany(Image::class, 'post');
    }
}

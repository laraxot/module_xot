<?php

namespace Modules\Xot\Models;

/**
 * Modules\Xot\Models\Conf
 *
 * @property int $id
 * @property string $appname
 * @property string $description
 * @property string $created_by
 * @property string $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Image[] $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder|Conf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conf query()
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereAppname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conf whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Conf extends BaseModel {
    //public $table = '';

    /**
     * @var string[]
     */
    public array $fillable = [
        'id', 'appname', 'description', 'keywords', 'author',
    ];
}

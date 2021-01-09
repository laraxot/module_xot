<?php

namespace Modules\Xot\Models;

//--- TRAITS ---
use Modules\Xot\Models\Traits\WidgetTrait;

//------ ext models---

/**
 * Modules\Xot\Models\Home
 *
 * @property int $id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $icon_src
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Widget[] $containerWidgets
 * @property-read int|null $container_widgets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Widget[] $widgets
 * @property-read int|null $widgets_count
 * @method static \Illuminate\Database\Eloquent\Builder|Home newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Home newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Home ofLayoutPosition($layout_position)
 * @method static \Illuminate\Database\Eloquent\Builder|Home query()
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereIconSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Home extends BaseModel {
    use WidgetTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['id', 'article_type', 'icon_src'];
}

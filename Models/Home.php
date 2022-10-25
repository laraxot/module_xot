<?php
/**
 * ---.
 */
declare(strict_types=1);

namespace Modules\Xot\Models;

use Modules\Xot\Models\Traits\WidgetTrait;
use Sushi\Sushi;

/**
 * Modules\Xot\Models\Home.
 *
 * @property int|null $id
 * @property string|null $name
 * @property string|null $icon_src
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Widget[] $containerWidgets
 * @property-read int|null $container_widgets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Widget[] $widgets
 * @property-read int|null $widgets_count
 * @method static \Modules\Xot\Database\Factories\HomeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Home newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Home newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Home ofLayoutPosition($layout_position)
 * @method static \Illuminate\Database\Eloquent\Builder|Home query()
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereIconSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Home whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Home extends BaseModel {
    use Sushi;
    use WidgetTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['id', 'name', 'icon_src', 'created_by', 'updated_by'];

    /**
     * Undocumented variable.
     *
     * @var array
     */
    protected $rows = [
        [
            'id' => 'home',
            'name' => 'New York',
            'icon_src' => '',
            'created_by' => 'xot',
            'updated_by' => 'xot',
        ],
    ];
}

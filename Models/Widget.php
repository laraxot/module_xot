<?php

declare(strict_types=1);

namespace Modules\Xot\Models;

//------ ext models---
use Modules\Xot\Models\Traits\WidgetTrait;

/**
 * Modules\Xot\Models\Widget.
 *
 * @property int                                                                  $id
 * @property string|null                                                          $post_type
 * @property string|null                                                          $layout_position
 * @property string|null                                                          $title
 * @property int|null                                                             $post_id
 * @property string|null                                                          $blade
 * @property string|null                                                          $image_src
 * @property int|null                                                             $pos
 * @property string|null                                                          $model
 * @property int|null                                                             $limit
 * @property string|null                                                          $order_by
 * @property \Illuminate\Support\Carbon|null                                      $created_at
 * @property string|null                                                          $created_by
 * @property \Illuminate\Support\Carbon|null                                      $updated_at
 * @property string|null                                                          $updated_by
 * @property \Illuminate\Database\Eloquent\Collection|Widget[]                    $containerWidgets
 * @property int|null                                                             $container_widgets_count
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Image[] $images
 * @property int|null                                                             $images_count
 * @property \Illuminate\Database\Eloquent\Model|\Eloquent                        $linked
 * @property \Illuminate\Database\Eloquent\Collection|Widget[]                    $widgets
 * @property int|null                                                             $widgets_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Widget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget ofLayoutPosition($layout_position)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget query()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereBlade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereImageSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereLayoutPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereOrderBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget wherePos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Widget extends BaseModel {
    use WidgetTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'post_type', 'post_id', //nullablemorph
        'title', 'subtitle',
        'blade', 'pos', 'model', 'limit',
        'order_by', 'image_src', 'layout_position',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function linked() {
        return $this->morphTo('post');
    }

    /**
     * @param mixed $value
     *
     * @return int|mixed
     */
    public function getPosAttribute($value) {
        if (null != $value) {
            return $value;
        }
        $value = self::max('pos') + 1;

        return $value;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function toHtml(array $params = null) {
        $view = 'pub_theme::layouts.widgets';
        if (null != $this->layout_position) {
            $view .= '.'.$this->layout_position;
        }
        $view .= '.'.$this->blade;
        $view_params = [
            'lang' => \App::getLocale(),
            'view' => $view,
            'row' => $this->linked,
            'widget' => $this,
        ];
        if (null != $params) {
            $view_params['params'] = $params;
        }
        if (! view()->exists($view)) {
            dddx(['View ['.$view.'] Not Exists !']);
        }
        try {
            return view($view)->with($view_params);
        } catch (\Exception $e) {
            dddx([$e]);
        }

        return view()->make($view)->with($view_params);
    }
}

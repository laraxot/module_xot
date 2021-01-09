<?php

namespace Modules\Xot\Models;

//------ ext models---

/**
 * Modules\Xot\Models\Metatag
 *
 * @property int $id
 * @property string|null $sitename
 * @property string|null $title
 * @property string|null $subtitle
 * @property string|null $charset
 * @property string|null $author
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $logo_src
 * @property string|null $logo_footer_src
 * @property string|null $tennant_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $updated_by
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Xot\Models\Image[] $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereCharset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereLogoFooterSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereLogoSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereSitename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereTennantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Metatag whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Metatag extends BaseModel {
    /**
     * @var string[]
     */
    protected $fillable = ['id', 'title', 'subtitle', 'charset', 'author',
        'meta_description', 'meta_keywords', 'logo_src', 'logo_footer_src',
        'tennant_name', 'sitename', 'created_at', 'created_by', 'updated_at', 'updated_by', ];
}

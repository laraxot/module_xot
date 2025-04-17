<?php

declare(strict_types=1);

namespace Modules\Xot\Models;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;
// ---- Traits ----
use Modules\Xot\Traits\Updater;

/**
 * Class XotBaseModel.
 */
abstract class XotBaseModel extends Model
{
    // use Searchable;
    use Updater;
    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @see  https://laravel-news.com/6-eloquent-secrets
     *
     * @var bool
     */
    public static $snakeAttributes = true;

    protected $perPage = 30;
}

<?php

declare(strict_types=1);

namespace Modules\Xot\Models;

/**
 * Modules\Xot\Models\Session.
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string      $payload
 * @property int         $last_activity
 *
 * @method static \Modules\Xot\Database\Factories\SessionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Session  newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Session  newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Session  query()
 * @method static \Illuminate\Database\Eloquent\Builder|Session  whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session  whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session  whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session  wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session  whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Session  whereUserId($value)
 *
 * @mixin IdeHelperSession
 * @mixin \Eloquent
 */
class Session extends BaseModel
{
    protected $fillable = ['id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity'];
}

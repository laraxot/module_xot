<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\ModelStatus\Status;

/**
 * Modules\Xot\Contracts\ModelWithStatusContract.
 *
 * @property int                                                                   $id
 * @property int|null                                                              $user_id
 * @property string|null                                                           $post_type
 * @property \Illuminate\Support\Carbon|null                                       $created_at
 * @property \Illuminate\Support\Carbon|null                                       $updated_at
 * @property string|null                                                           $created_by
 * @property string|null                                                           $updated_by
 * @property string|null                                                           $title
 * @property PivotContract|null                                                    $pivot
 * @property string                                                                $tennant_name
 * @property \Modules\User\Models\User|null                                        $user
 * @property string                                                                $status
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\ModelStatus\Status[] $statuses
 * @property int|null                                                              $statuses_count
 *
 * @method mixed     getKey()
 * @method string    getRouteKey()
 * @method string    getRouteKeyName()
 * @method string    getTable()
 * @method mixed     with($array)
 * @method array     getFillable()
 * @method mixed     fill($array)
 * @method mixed     getConnection()
 * @method mixed     update($params)
 * @method mixed     delete()
 * @method mixed     detach($params)
 * @method mixed     attach($params)
 * @method mixed     save($params)
 * @method array     treeLabel()
 * @method array     treeSons()
 * @method int       treeSonsCount()
 * @method mixed     bellBoys()
 * @method array     toArray()
 * @method BelongsTo user()
 *
 * @mixin  \Eloquent
 */
interface ModelWithStatusContract
{
    public function statuses(): MorphMany;

    public function status(): ?Status;

    public function setStatus(string $name, string $reason = null): self;
}

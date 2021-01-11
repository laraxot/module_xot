<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Food\Contracts\BellBoyContract;
use Modules\Food\Models\RestaurantOwner;

/**
 * Modules\Xot\Contracts\PanelContract.
 *
 * @property int                             $id
 * @property int|null                        $auth_user_id
 * @property string|null                     $post_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $created_by
 * @property string|null                     $updated_by
 * @property string|null                     $title
 * @property Collection|BellBoyContract[]    $bellBoys
 * @property Collection|RestaurantOwner[]    $restaurantOwners
 * @property bool                            $is_reclamed
 * @property bool                            $table_enable
 * @property PivotContract|null              $pivot
 *
 * @method mixed  getKey()
 * @method bool   isBellBoyAuthID($auth_user_id)
 * @method string getRouteKey()
 * @method string getRouteKeyName()
 * @method string getTable()
 * @method mixed  with($array)
 * @method array  getFillable()
 * @method mixed  fill($array)
 * @method mixed  getConnection()
 */
interface ModelContract {
}

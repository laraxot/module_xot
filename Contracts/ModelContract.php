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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $created_by
 * @property string|null                     $updated_by
 * @property string|null                     $title
 * @property Collection|BellBoyContract[]    $bellBoys
 * @property Collection|RestaurantOwner[]    $restaurantOwners
 * @property bool                            $is_reclamed
 * @property bool                            $table_enable
 *
 * @method mixed getKey()
 * @method bool  isBellBoyAuthID($auth_user_id)
 */
interface ModelContract {
}

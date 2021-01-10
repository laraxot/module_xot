<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

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
 *
 * @method mixed getKey()
 */
interface ModelContract {
}

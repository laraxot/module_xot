<?php

// senza la document delle property phpstan da errore per proprieta' mancante

declare(strict_types=1);

namespace Modules\Xot\Contracts;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modules\User\Contracts\UserContract.
 *
 * @property ModelProfileContract|null                                            $profile
 * @property int                                                                  $id
 * @property string                                                               $handle
 * @property string|null                                                          $first_name
 * @property string|null                                                          $last_name
 * @property string|null                                                          $full_name
 * @property string|null                                                          $phone
 * @property string|null                                                          $email
 * @property \Illuminate\Database\Eloquent\Collection|\Modules\User\Models\Area[] $areas
 * @property \Modules\User\Models\PermUser|null                                   $perm
 *
 * @mixin    \Eloquent
 */
interface UserContract extends MustVerifyEmail
{
    /*
    public function isSuperAdmin();
    public function name();
    public function areas();
    public function avatar();
    */
    public function profile(): HasOne;

    /**
     * Undocumented function.
     *
     * @return bool
     */
    public function update(array $attributes = [], array $options = []);

    /**
     * Get a relationship.
     *
     * @param string $key
     *
     * @return mixed|void
     */
    public function getRelationValue($key);

    /**
     * Undocumented function.
     *
     * @return Model
     */
    public function newInstance();

    /**
     * Summary of getKey.
     *
     * @return string|int
     */
    public function getKey();
}

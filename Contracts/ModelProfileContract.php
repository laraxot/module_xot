<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

use Spatie\Permission\Exceptions\PermissionDoesNotExist;

/**
 * Modules\Xot\Contracts\ModelProfileContract.
 *
 * @property string                                                                   $id
 * @property string                                                                   $email
 * @property \Illuminate\Database\Eloquent\Collection<int, \Modules\User\Models\Role> $roles
 * @property int|null                                                                 $roles_count
 *
 * @mixin \Eloquent
 */
interface ModelProfileContract extends ModelContract
{
    /**
     * Grant the given permission(s) to a role.
     *
     * @param string|int|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function givePermissionTo(...$permissions);

    /**
     * Assign the given role to the model.
     *
     * @param array|string|int|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection ...$roles
     *
     * @return $this
     */
    public function assignRole(...$roles);

    /**
     * Determine if the model has (one of) the given role(s).
     *
     * @param string|int|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     */
    public function hasRole($roles, string $guard = null): bool;

    /**
     * Determine if the model has any of the given role(s).
     *
     * Alias to hasRole() but without Guard controls
     *
     * @param string|int|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     */
    public function hasAnyRole(...$roles): bool;

    /**
     * Determine if the model may perform the given permission.
     *
     * @param string|int|\Spatie\Permission\Contracts\Permission $permission
     * @param string|null                                        $guardName
     *
     * @throws PermissionDoesNotExist
     */
    public function hasPermissionTo($permission, $guardName = null): bool;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query);
}

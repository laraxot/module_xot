<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
//use Modules\LU\Models\User;
use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\UserContract as User;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePolicy.
 */
abstract class XotBasePolicy {
    use HandlesAuthorization;

    /**
     * @param UserContract $user
     * @param $ability
     *
     * @return bool
     */
    public function before($user, $ability) {
        if (is_object($user) && Panel::get($user)->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * @param ModelContract $post
     */
    public function index(?User $user, ModelContract $post): bool {
        return true;
    }

    /**
     * @param ModelContract $post
     */
    public function show(?User $user, ModelContract $post): bool {
        return true;
    }

    /**
     * @param ModelContract $post
     */
    public function create(User $user, ModelContract $post): bool {
        return true;
    }

    /**
     * @param ModelContract $post
     */
    public function edit(User $user, ModelContract $post): bool {
        //return true;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function update(User $user, ModelContract $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function store(User $user, ModelContract $post) {
        /*
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }
        return false;
        non e' stato creato..
        */
        return true;
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function indexAttach(User $user, ModelContract $post) {
        return true;
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function indexEdit(User $user, ModelContract $post) {
        return true;
    }

    /**
     * @param ModelContract $post
     *
     * @return false
     */
    public function updateTranslate(User $user, ModelContract $post) {
        return false; //update-translate di @can()
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function destroy(User $user, ModelContract $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function delete(User $user, ModelContract $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function restore(User $user, ModelContract $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param ModelContract $post
     */
    public function forceDelete(User $user, ModelContract $post) {
    }

    /**
     * @param ModelContract $post
     *
     * @return bool
     */
    public function detach(User $user, ModelContract $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(User $user, ModelContract $post) {
        return true;
    }

    /**
     * Determine whether the user can view any DocDummyPluralModel.
     *
     * @return mixed
     */
    public function viewAny(User $user) {
    }

    /**
     * @param ModelContract $post
     */
    public function view(User $user, ModelContract $post) {
    }
}

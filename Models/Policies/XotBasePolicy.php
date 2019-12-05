<?php

namespace Modules\Xot\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
//use Modules\LU\Models\User;
use Modules\Xot\Contracts\UserContract as User;
use Modules\Xot\Services\PanelService as Panel;

abstract class XotBasePolicy {
    use HandlesAuthorization;

    public function before($user, $ability) {
        if (is_object($user) && Panel::get($user)->isSuperAdmin()) {
            return true;
        }
    }

    public function index(?User $user, $post) {
        return true;
    }

    public function show(?User $user, $post) {
        return true;
    }

    public function create(User $user, $post) {
        return true;
    }

    public function edit(User $user, $post) {
        //return true;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function update(User $user, $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function store(User $user, $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function indexAttach(User $user, $post) {
        return true;
    }

    public function indexEdit(User $user, $post) {
        return true;
    }

    public function updateTranslate(User $user, $post) {
        return false; //update-translate di @can()
    }

    public function destroy(User $user, $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the DocDummyModel.
     *
     * @param \Modules\LU\Models\User      $user
     * @param \Modules\Blog\Models\Privacy $dummyModel
     *
     * @return mixed
     */
    public function delete(User $user, $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the DocDummyModel.
     *
     * @param \Modules\LU\Models\User      $user
     * @param \Modules\Blog\Models\Privacy $dummyModel
     *
     * @return mixed
     */
    public function restore(User $user, $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the DocDummyModel.
     *
     * @param \Modules\LU\Models\User      $user
     * @param \Modules\Blog\Models\Privacy $dummyModel
     *
     * @return mixed
     */
    public function forceDelete(User $user, $post) {
    }

    public function detach(User $user, $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(User $user, $post) {
        return true;
    }

    /**
     * Determine whether the user can view any DocDummyPluralModel.
     *
     * @param \Modules\LU\Models\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user) {
    }

    /**
     * Determine whether the user can view the DocDummyModel.
     *
     * @param \Modules\LU\Models\User      $user
     * @param \Modules\Blog\Models\Privacy $dummyModel
     *
     * @return mixed
     */
    public function view(User $user, $post) {
    }
}

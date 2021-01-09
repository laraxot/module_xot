<?php

namespace Modules\Xot\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
//use Modules\LU\Models\User;
use Modules\Xot\Contracts\UserContract as User;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePolicy
 * @package Modules\Xot\Models\Policies
 */
abstract class XotBasePolicy {
    use HandlesAuthorization;

    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability) {
        if (is_object($user) && Panel::get($user)->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * @param User|null $user
     * @param $post
     * @return bool
     */
    public function index(?User $user, $post) {
        return true;
    }

    /**
     * @param User|null $user
     * @param $post
     * @return bool
     */
    public function show(?User $user, $post) {
        return true;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function create(User $user, $post) {
        return true;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function edit(User $user, $post) {
        //return true;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function update(User $user, $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function store(User $user, $post) {
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
     * @param User $user
     * @param $post
     * @return bool
     */
    public function indexAttach(User $user, $post) {
        return true;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function indexEdit(User $user, $post) {
        return true;
    }

    /**
     * @param User $user
     * @param $post
     * @return false
     */
    public function updateTranslate(User $user, $post) {
        return false; //update-translate di @can()
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function destroy(User $user, $post) {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function delete(User $user, $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
    public function restore(User $user, $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $post
     */
    public function forceDelete(User $user, $post) {
    }

    /**
     * @param User $user
     * @param $post
     * @return bool
     */
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
     * @param User $user
     *
     * @return mixed
     */
    public function viewAny(User $user) {
    }

    /**
     * @param User $user
     * @param $post
     */
    public function view(User $user, $post) {
    }
}

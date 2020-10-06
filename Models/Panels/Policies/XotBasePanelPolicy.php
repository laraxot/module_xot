<?php

namespace Modules\Xot\Models\Panels\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
//use Modules\LU\Models\User;
use Modules\Xot\Contracts\UserContract as User;
use Modules\Xot\Services\PanelService as Panel;

abstract class XotBasePanelPolicy {
    use HandlesAuthorization;

    public function before($user, $ability) {
        if (is_object($user) && Panel::get($user)->isSuperAdmin()) {
            return true;
        }
    }

    public function index(?User $user, $panel) {
        return true;
    }

    public function show(?User $user, $panel) {
        return true;
    }

    public function create(User $user, $panel) {
        return true;
    }

    public function edit(User $user, $panel) {
        //return true;
        $post = $panel->row;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    public function update(User $user, $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    public function store(User $user, $panel) {
        /*
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }
        return false;
        non e' stato creato..
        */
        return true;
    }

    public function indexAttach(User $user, $panel) {
        return true;
    }

    public function indexEdit(User $user, $panel) {
        return true;
    }

    public function updateTranslate(User $user, $panel) {
        return false; //update-translate di @can()
    }

    public function destroy(User $user, $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function delete(User $user, $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function restore(User $user, $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function forceDelete(User $user, $panel) {
    }

    public function detach(User $user, $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(User $user, $panel) {
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

    public function view(User $user, $panel) {
    }
}

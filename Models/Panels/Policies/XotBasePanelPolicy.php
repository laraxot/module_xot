<?php

namespace Modules\Xot\Models\Panels\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Illuminate\Contracts\Auth\UserProvider as User;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePanelPolicy
 * @package Modules\Xot\Models\Panels\Policies
 */
abstract class XotBasePanelPolicy {
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
     * @param UserContract|null $user
     * @param PanelContract $panel
     * @return bool
     */
    public function index(?UserContract $user, PanelContract $panel) {
        return true;
    }

    /**
     * @param UserContract|null $user
     * @param PanelContract $panel
     * @return bool
     */
    public function show(?UserContract $user, PanelContract $panel) {
        return true;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function create(UserContract $user, PanelContract $panel) {
        return true;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function edit(UserContract $user, PanelContract $panel) {
        //return true;
        $post = $panel->row;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function update(UserContract $user, PanelContract $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function store(UserContract $user, PanelContract $panel) {
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
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function indexAttach(UserContract $user, PanelContract $panel) {
        return true;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function indexEdit(UserContract $user, PanelContract $panel) {
        return true;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return false
     */
    public function updateTranslate(UserContract $user, PanelContract $panel) {
        return false; //update-translate di @can()
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function destroy(UserContract $user, PanelContract $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function delete(UserContract $user, PanelContract $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function restore(UserContract $user, PanelContract $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     */
    public function forceDelete(UserContract $user, PanelContract $panel) {
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     * @return bool
     */
    public function detach(UserContract $user, PanelContract $panel) {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(UserContract $user, PanelContract $panel) {
        return true;
    }

    /**
     * Determine whether the user can view any DocDummyPluralModel.
     *
     * @param UserContract $user
     * @return mixed
     */
    public function viewAny(UserContract $user) {
    }

    /**
     * @param UserContract $user
     * @param PanelContract $panel
     */
    public function view(UserContract $user, PanelContract $panel) {
    }
}

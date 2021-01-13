<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Illuminate\Contracts\Auth\UserProvider as User;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePanelPolicy.
 */
abstract class XotBasePanelPolicy {
    use HandlesAuthorization;

    /**
     * @param UserContract $user
     * @param string       $ability
     *
     * @return bool|null
     */
    public function before($user, $ability) {
        if (is_object($user) && Panel::get($user)->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function index(?UserContract $user, PanelContract $panel): bool {
        return true;
    }

    public function show(?UserContract $user, PanelContract $panel): bool {
        return true;
    }

    public function create(UserContract $user, PanelContract $panel): bool {
        return true;
    }

    public function edit(UserContract $user, PanelContract $panel): bool {
        //return true;
        $post = $panel->row;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    public function update(UserContract $user, PanelContract $panel): bool {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    public function store(UserContract $user, PanelContract $panel): bool {
        /*
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }
        return false;
        non e' stato creato..
        */
        return true;
    }

    public function indexAttach(UserContract $user, PanelContract $panel): bool {
        return true;
    }

    public function indexEdit(UserContract $user, PanelContract $panel): bool {
        return true;
    }

    /**
     * @return false
     */
    public function updateTranslate(UserContract $user, PanelContract $panel): bool {
        return false; //update-translate di @can()
    }

    public function destroy(UserContract $user, PanelContract $panel): bool {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function delete(UserContract $user, PanelContract $panel): bool {
        $post = $panel->row;

        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function restore(UserContract $user, PanelContract $panel): bool {
        $post = $panel->row;

        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function forceDelete(UserContract $user, PanelContract $panel): bool {
        return false;
    }

    public function detach(UserContract $user, PanelContract $panel): bool {
        $post = $panel->row;

        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(UserContract $user, PanelContract $panel): bool {
        return true;
    }

    /**
     * Determine whether the user can view any DocDummyPluralModel.
     */
    public function viewAny(UserContract $user): bool {
        return true;
    }

    public function view(UserContract $user, PanelContract $panel): bool {
        return true;
    }
}

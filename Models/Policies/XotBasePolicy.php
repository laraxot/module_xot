<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Illuminate\Database\Eloquent\Model as Post;
//use Modules\LU\Models\User;
use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePolicy.
 */
abstract class XotBasePolicy {
    use HandlesAuthorization;

    /**
     * @param UserContract $user
     * @param string       $ability
     *
     * @return bool|null
     */
    public function before(?UserContract $user, $ability) {
        if (is_object($user) && Panel::get($user)->isSuperAdmin()) {
            return true;
        }

        return false;
    }

    public function index(?UserContract $user, ModelContract $post): bool {
        return true;
    }

    public function show(?UserContract $user, ModelContract $post): bool {
        return true;
    }

    public function create(UserContract $user, ModelContract $post): bool {
        return true;
    }

    public function edit(UserContract $user, ModelContract $post): bool {
        //return true;
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    public function update(UserContract $user, ModelContract $post): bool {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle || $post->auth_user_id == $user->auth_user_id) {
            return true;
        }

        return false;
    }

    public function store(UserContract $user, ModelContract $post): bool {
        /*
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }
        return false;
        non e' stato creato..
        */
        return true;
    }

    public function indexAttach(UserContract $user, ModelContract $post): bool {
        return true;
    }

    public function indexEdit(UserContract $user, ModelContract $post): bool {
        return true;
    }

    /**
     * @return false
     */
    public function updateTranslate(UserContract $user, ModelContract $post): bool {
        return false; //update-translate di @can()
    }

    public function destroy(UserContract $user, ModelContract $post): bool {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function delete(UserContract $user, ModelContract $post): bool {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function restore(UserContract $user, ModelContract $post): bool {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function forceDelete(UserContract $user, ModelContract $post): mixed {
    }

    public function detach(UserContract $user, ModelContract $post): bool {
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(UserContract $user, ModelContract $post): bool {
        return true;
    }

    /**
     * Determine whether the user can view any DocDummyPluralModel.
     *
     * @return mixed
     */
    public function viewAny(UserContract $user): mixed {
    }

    public function view(UserContract $user, ModelContract $post): mixed {
    }
}

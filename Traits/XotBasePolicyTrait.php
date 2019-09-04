<?php

namespace Modules\Xot\Traits;

use Illuminate\Auth\Access\HandlesAuthorization;
//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
use Modules\LU\Models\User;

/*-- se faccio l'extends
Declaration of Modules\Blog\Models\Policies\EventPolicy::index(Modules\LU\Models\User $user, Modules\Blog\Models\Event $post) should be compatible with Modules\Xot\Models\Policies\XotBasePolicy::index(Modules\LU\Models\User $user,Post $post)
-- */

trait XotBasePolicyTrait {
    use HandlesAuthorization;

    public function before($user, $ability) {
        if (is_object($user->perm) && $user->perm->perm_type >= 5) {  //superadmin
            return true;
        }
    }

    public function index(?User $user, $post) {
        return true;
    }

    public function create(User $user, $post) {
        return true;
    }

    public function edit(User $user, $post) {
        if (null == $post->created_by) {
            $post->created_by = $post->post->created_by;
            $post->save();
        }

        if (null == $post->updated_by) {
            $post->updated_by = $post->post->updated_by;
            $post->save();
        }
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function update(User $user, $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function store(User $user, $post) {
        if ($post->created_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function show(User $user, $post) {
        return true; // perche' false ?? dovrei mettere lo status pubblicato
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
        return true;
    }

    public function detach(User $user, $post) {
        return true;
    }

    public function clone(User $user, $post) {
        return true;
    }
}

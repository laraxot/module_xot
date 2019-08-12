<?php
namespace Modules\Xot\Models\Policies;
use Illuminate\Auth\Access\HandlesAuthorization;

//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
use Modules\LU\Models\User;

use Modules\Xot\Traits\XotBasePolicyTrait;

abstract class XotBasePolicy {
    use HandlesAuthorization;

    public function before($user, $ability){
        if (is_object($user->perm) && $user->perm->perm_type >= 5) {  //superadmin
            return true;
        }
    }

    public function index(?User $user, $post){
        return true;
    }

    public function show(?User $user, $post){
        return true; 
    }


    public function create(User $user,$post){
        return true;
    }

    public function edit(User $user, $post){
        /*
        if($post->created_by==null){
            $post->created_by=$post->post->created_by;
            $post->save();
        }

        if($post->updated_by==null){
            $post->updated_by=$post->post->updated_by;
            $post->save();
        }
        */
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }
    
    public function update(User $user, $post){
        
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function store(User $user,$post){
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    

    public function indexAttach(User $user, $post){
        return true;
    }

    
    public function indexEdit(User $user, $post){
        return true;
    }


    public function updateTranslate(User $user, $post){
        return false; //update-translate di @can()
    }

    public function destroy(User $user, $post){
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function detach(User $user, $post){
        if ($post->created_by == $user->handle || $post->updated_by == $user->handle) {
            return true;
        }

        return false;
    }

    public function clone(User $user, $post){
        return true;
    }


}

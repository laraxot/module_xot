<?php
namespace Modules\Xot\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\LU\Models\User as User;
use Modules\Xot\Models\Metatag as Post;

use Modules\Xot\Models\Policies\XotBasePolicy;

/**
 * Class MetatagPolicy
 * @package Modules\Xot\Models\Policies
 */
class MetatagPolicy extends XotBasePolicy {

    /**
     * @param UserContract $user
     * @param $metatag
     * @return false
     */
    public function storeFileMetatag($user, $metatag){
        //return ($metatag->tennant_name=='foodlocal');
        return false;
    }
}

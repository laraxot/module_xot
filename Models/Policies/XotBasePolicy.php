<?php
namespace Modules\Xot\Models\Policies;
use Illuminate\Auth\Access\HandlesAuthorization;

//use Modules\Food\Models\Restaurant as Post;
//use Illuminate\Database\Eloquent\Model as Post;
use Modules\LU\Models\User;

use Modules\Xot\Traits\XotBasePolicyTrait;

abstract class XotBasePolicy {
    use HandlesAuthorization;
    use XotBasePolicyTrait;


}

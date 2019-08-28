<?php

namespace Modules\Xot\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\LU\Models\User as User;
use Modules\Xot\Models\Image as Post; 

use Modules\Xot\Traits\XotBasePolicyTrait;

use Modules\Xot\Models\Policies\XotBasePolicy;

class ImagePolicy extends XotBasePolicy{

	public function store(User $user,$post){
		return true; //da aggiungere pezzi
	}
    
}

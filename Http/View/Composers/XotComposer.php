<?php

namespace Modules\Xot\Http\View\Composers;

//use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\LU\Services\ProfileService;

class XotComposer {
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    //protected $users;

    /**
     * Create a new profile composer.
     *
     * @param UserRepository $users
     *
     * @return void
     */
    //public function __construct(UserRepository $users)
    //{
    // Dependencies automatically resolved by service container...
    //    $this->users = $users;
    //}

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view) {
        //$view->with('count', $this->users->count());
        $profile = ProfileService::get(Auth::user());
        //dddx($profile->avatar());
        $view->with('profile', $profile);
    }
}

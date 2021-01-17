<?php

declare(strict_types=1);

namespace Modules\Xot\Http\View\Composers;

//use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Modules\LU\Services\ProfileService;

/**
 * Class XotComposer.
 */
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
        $profile = ProfileService::get(Auth::user());
        $lang = app()->getLocale();
        $params = [];
        $route_current = Route::current();
        if (null != $route_current) {
            $params = $route_current->parameters();
        }

        $view->with('params', $params);
        $view->with('lang', $lang);
        $view->with('profile', $profile);
    }
}

<?php

namespace Modules\Xot\Http\Middleware;

use Modules\Xot\Presenters\HtmlPanelPresenter;
use Modules\Xot\Presenters\JsonPanelPresenter;


/**
 * Class SelectResponseMiddleware
 * @package Modules\Xot\Http\Middleware
 */
class SelectResponseMiddleware {
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed ...$guards
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$guards) {
        $responseType = $request['responseType'];
        /*
        $responses=[
            'html'=> HtmlPanelPresenter::class,
            'json'=>JsonPanelPresenter::class,
        ];
        if(isset($responses[$responseType])){
            //dddx($responses[$responseType]);
            app()->bind(
                PanelPresenterContract::class,
                $responses[$responseType],
            );

        }
        */

        /*
        $this->app->bind(
            PanelPresenterContract::class,
            HtmlPanelPresenter::class,
        );
        */

        //dddx('qui');
        //if ($responseType) {
        //    \Presenter::select($responseType);
        //}



        return $next($request);
    }
}

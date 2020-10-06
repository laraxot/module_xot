<?php

use Modules\Xot\Services\RouteDynService;

$namespace = '\Modules\Xot';
$pack = class_basename($namespace);

$namespace .= '\Http\Controllers';
//$middleware = ['web', 'guest']; //guest ti riindirizza se non sei loggato
$middleware = ['web', \Modules\Xot\Http\Middleware\PanelMiddleware::class];

//$areas_prgs = include __DIR__ . '/web_common.php';
$areas_prgs = RouteDynService::generate();
//$prefix = App::getLocale();
if (! config('xra.disable_frontend_dynamic_route')) {
    $prefix = '{lang}';
    Route::group(
        [
            'prefix' => $prefix,
            'middleware' => $middleware,
            'namespace' => $namespace,
        ],
        function () use ($areas_prgs, $namespace) {
            RouteDynService::dynamic_route($areas_prgs, null, $namespace);
            Route::get('/', 'HomeController@show')->name('home');
            Route::get('/home', 'HomeController@show')->name('home');
        }
    );

    //dddx(config());

    //dddx([$_SERVER, parseUrl($_SERVER['SERVER_NAME'])]);
    //Route::domain('food.local')->group(function () use ($middleware,$namespace) {
    Route::group(
        [
            'prefix' => null,
            'middleware' => $middleware,
            'namespace' => $namespace,
        ],
        function () {
            Route::get('/', 'HomeController@show')->name('home'); //show o index ? homecontrller@show o pagecontroller@home ?
            Route::get('/home', 'HomeController@show')->name('home'); //togliere o tenere ?
            Route::get('/redirect', 'HomeController@redirect')->name('redirect');
            //Route::get('/test01',   'HomeController@test01');
        }
    );
    //});
}
$middleware = ['web', 'auth'/*,'verified'*/];
$prefix = 'admin';

Route::group(
    [
        'prefix' => $prefix,
        'middleware' => $middleware,
        'namespace' => $namespace.'\Admin',
    ],
    function () use ($areas_prgs) {
        Route::get('/', 'BackendController@dashboard')->name('admin');
        //RouteTrait::dynamic_route($areas_prgs);
    }
);

if (in_admin()) {
    //require_once(__DIR__.'/web_admin.php');  //WEB GENERICO
    $areas_adm = [
        //$item1,
        [
            'name' => '{module}',
            'as' => 'admin.',
            'param_name' => 'lang',  //ero titubante su questo
            'only' => ['index'],
            'subs' => $areas_prgs,
        ],
        //$item0,
    ];
    $prefix = 'admin';
    $middleware = ['web', 'auth', \Modules\Xot\Http\Middleware\PanelMiddleware::class];
    $namespace .= '\Admin';
    Route::group(
        [
            'prefix' => $prefix,
            'middleware' => $middleware,
            'namespace' => $namespace,
        ],
        function () use ($areas_adm, $namespace) {
            RouteDynService::dynamic_route($areas_adm, null, $namespace);
        }
    );
}

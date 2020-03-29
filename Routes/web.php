<?php

use Modules\Xot\Services\RouteService;

$namespace = '\Modules\Xot';
$pack = class_basename($namespace);

$namespace .= '\Http\Controllers';
//$middleware = ['web', 'guest']; //guest ti riindirizza se non sei loggato
$middleware = ['web'];

$areas_prgs = include __DIR__.'/web_common.php';
//$prefix = App::getLocale();
$prefix = '{lang}';
Route::group(
    [
        'prefix' => $prefix,
        'middleware' => $middleware,
        'namespace' => $namespace,
    ],
    function () use ($areas_prgs,$namespace) {
        RouteService::dynamic_route($areas_prgs, null, $namespace);
        Route::get('/', 'HomeController@index');
        Route::get('/home', 'HomeController@index');
    }
);

Route::group(
    [
        'prefix' => null,
        'middleware' => $middleware,
        'namespace' => $namespace,
    ],
    function () {
        Route::get('/', 'HomeController@index');
        Route::get('/home', 'HomeController@index'); //togliere o tenere ?
        Route::get('/redirect', 'HomeController@redirect');
        //Route::get('/test01',   'HomeController@test01');
    }
);

$middleware = ['web', 'auth'/*,'verified'*/];
$prefix = 'admin';

Route::group(
    [
        'prefix' => $prefix,
        'middleware' => $middleware,
        'namespace' => $namespace.'\Admin',
    ],
    function () use ($areas_prgs) {
        Route::get('/', 'BackendController@dashboard');
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
    $middleware = ['web', 'auth'];
    $namespace .= '\Admin';
    Route::group(
        [
            'prefix' => $prefix,
            'middleware' => $middleware,
            'namespace' => $namespace,
        ],
        function () use ($areas_adm,$namespace) {
            RouteService::dynamic_route($areas_adm, null, $namespace);
        }
    );
}

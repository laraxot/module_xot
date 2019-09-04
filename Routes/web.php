<?php

use Modules\Xot\Services\RouteService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::prefix('xot')->group(function() {
    Route::get('/', 'XotController@index');
});
*/

//if(\Request::segment(1)=='admin'  /* && \Request::segment(2)=='blog' */){
$namespace = '\Modules\Xot'; //$this->getNamespace();
$pack = class_basename($namespace);

$namespace .= '\Http\Controllers';
$middleware = ['web', 'guest']; //guest ti riindirizza se non sei loggato
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

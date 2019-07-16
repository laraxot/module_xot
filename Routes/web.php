<?php
use Modules\Extend\Services\RouteService;
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

$namespace = '\Modules\Xot';//$this->getNamespace();
$pack = class_basename($namespace);

$namespace .= '\Http\Controllers';
$middleware = ['web', 'guest']; //guest ti riindirizza se non sei loggato
$middleware = ['web'];

$item2=include(__DIR__.'/web_common.php');
/// da spostare anche in ADMIN
$areas_prgs = [
        [
            'name' => '{container0}',
            'param_name' => '',
            'as'=>'container0.index_', 
            'acts'=>[
                [
                    'name'=>'Edit',
                    'act'=>'indexEdit',
                ],
            ],
        ],
    $item2,
];

$prefix = App::getLocale();  
Route::group(
    [
    'prefix' => $prefix,
    'middleware' => $middleware,
    'namespace' => $namespace,
    ],
    function () use ($areas_prgs,$namespace) {
        RouteService::dynamic_route($areas_prgs, null, $namespace);
        Route::get('/',         'HomeController@index');
        Route::get('/home',     'HomeController@index');
    }
);

Route::group(
    [
        'prefix' => null, 
        'middleware' => $middleware, 
        'namespace' => $namespace
    ], 
    function () {
        Route::get('/',         'HomeController@index');
        Route::get('/home',     'HomeController@index'); //togliere o tenere ?
        Route::get('/redirect', 'HomeController@redirect');
        //Route::get('/test01',   'HomeController@test01');
    }
);

if(in_admin()){  
    require_once(__DIR__.'/web_admin.php');  //WEB GENERICO 
}
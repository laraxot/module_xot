<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

use Illuminate\Cache\TagSet;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Translation\Translator;
//use Illuminate\Http\Request;
use Laravel\Scout\EngineManager;
use Modules\Xot\Contracts\PanelPresenterContract;
//use Modules\Xot\Engines\Opcache;
//--- services ---
use Modules\Xot\Engines\FullTextSearchEngine;
use Modules\Xot\Http\View\Composers\XotComposer;
use Modules\Xot\Presenters\GeoJsonPanelPresenter;
use Modules\Xot\Presenters\HtmlPanelPresenter;
use Modules\Xot\Presenters\JsonPanelPresenter;
use Modules\Xot\Services\TenantService as Tenant; // per slegarmi da tntsearch
use Modules\Xot\Services\TranslatorService; // per dizionario morph

/**
 * Class XotServiceProvider.
 */
class XotServiceProvider extends XotBaseServiceProvider {
    /**
     * The module directory.
     */
    protected string $module_dir = __DIR__;

    /**
     * The module namespace.
     */
    protected string $module_ns = __NAMESPACE__;

    public string $module_name = 'xot';

    public function bootCallback() {
        $this->mergeConfigs();

        if (\Request::has('act') && 'migrate' == \Request::input('act')) {
            DB::purge('mysql'); //Call to a member function prepare() on null
            DB::reconnect('mysql');
        }
        //DB::purge(); //Call to a member function prepare() on null
        //* //Database connection [mysql] not configured.
        DB::reconnect();
        Schema::defaultStringLength(191);
        //*/
        $map = config('xra.model');

        Relation::morphMap($map);

        /*
        $morph_map=Relation::morphMap();
        ddd($morph_map);
        ddd(Relation::$morphMap);
        //*/

        $this->commands([
            \Modules\Xot\Console\CreateAllRepositoriesCommand::class,
            \Modules\Xot\Console\PanelMakeCommand::class,
            \Modules\Xot\Console\FixProvidersCommand::class,
        ]);
        if (config('xra.forcessl')) {
            // --- meglio ficcare un controllo anche sull'env
            if (isset($_SERVER['SERVER_NAME']) && 'localhost' != $_SERVER['SERVER_NAME']
                && isset($_SERVER['REQUEST_SCHEME']) && 'http' == $_SERVER['REQUEST_SCHEME']
            ) {
                URL::forceScheme('https');
                /*
                 * da fare in htaccess
                 **/
                if (! request()->secure() /* && in_array(env('APP_ENV'), ['stage', 'production']) */) {
                    exit(redirect()->secure(request()->getRequestUri()));
                }
            }
        }
        //*
        $this->registerTranslator();
        //$this->registerCacheOPCache();
        //*/
        resolve(EngineManager::class)->extend('fulltext', function () {
            return new FullTextSearchEngine();
        });

        //$this->registerLivewireComponents();
        $this->registerViewComposers();
    }

    //end bootCallback

    private function registerViewComposers() {
        //Factory $view
        //$view->composer('bootstrap-italia::page', BootstrapItaliaComposer::class);
        View::composer('*', XotComposer::class);
        //dddx($res);
    }

    public function mergeConfigs() {
        $configs = ['database', 'filesystems', 'auth', 'metatag', 'services', 'xra', 'social'];
        foreach ($configs as $v) {
            $tmp = Tenant::config($v);
            //ddd($tmp);
        }
        //DB::purge('mysql');//Call to a member function prepare() on null
        //DB::purge('liveuser_general');
        //DB::reconnect();
    }

    //end mergeConfigs

    public function registerCallback() {
        $this->loadHelpersFrom(__DIR__.'/../Helpers');
        $loader = AliasLoader::getInstance();
        $loader->alias('Panel', 'Modules\Xot\Services\PanelService');

        $responseType = request()->input('responseType');
        $responses = [
            //'html'=> HtmlPanelPresenter::class,//default
            'json' => JsonPanelPresenter::class,
            'geoJson' => GeoJsonPanelPresenter::class,
            //'pdf'=>PdfPanelPresenter::class,
            //'xls'=>XlsPanelPresenter::class,
        ];
        $response = HtmlPanelPresenter::class;
        if (isset($responses[$responseType])) {
            $response = $responses[$responseType];
        }

        $this->app->bind(
            PanelPresenterContract::class,
            //HtmlPanelPresenter::class,
            $response,
        );
    }

    /**
     * @param $path
     */
    public function loadHelpersFrom($path) {
        foreach (\glob($path.'/*.php') as $filename) {
            $filename = \str_replace('/', \DIRECTORY_SEPARATOR, $filename);
            require_once $filename;
        }
    }

    public function registerTranslator() {
        // Override the JSON Translator
        $this->app->extend('translator', function (Translator $translator) {
            $trans = new TranslatorService($translator->getLoader(), $translator->getLocale());
            $trans->setFallback($translator->getFallback());

            return $trans;
        });
    }

    public function registerCacheOPCache() {
        Cache::extend('opcache', function () {
            $store = new \Modules\Xot\Engines\Opcache\Store();

            return new \Modules\Xot\Engines\Opcache\Repository($store, new TagSet($store));
        });
        // Extend Collection to implement __set_state magic method
        if (! Collection::hasMacro('__set_state')) {
            Collection::macro('__set_state', function (array $array) {
                return new Collection($array['items']);
            });
        }
    }
} //end class

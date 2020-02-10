<?php

namespace Modules\Xot\Providers;

use Illuminate\Cache\TagSet;
use Illuminate\Database\Eloquent\Relations\Relation; // per dizionario morph
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Translation\Translator;
//use Illuminate\Http\Request;
use Laravel\Scout\EngineManager; // per slegarmi da tntsearch
use Modules\Xot\Engines\FullTextSearchEngine;
use Modules\Xot\Engines\Opcache;
use Modules\Xot\Services\TranslatorService;

class XotServiceProvider extends XotBaseServiceProvider {
    protected $module_dir = __DIR__;
    protected $module_ns = __NAMESPACE__;
    public $module_name = 'xot';

    public function bootCallback() {
        $this->mergeConfigs();

        if (\Request::has('act') && 'migrate' == \Request::input('act')) {
            DB::purge('mysql'); //Call to a member function prepare() on null
            DB::reconnect('mysql');
        }
        //DB::purge(); //Call to a member function prepare() on null
        DB::reconnect();
        $map = config('xra.model');
        //$route_params = Route::current()->parameters();
        //ddd($route_params);
        Relation::morphMap($map);
        $this->commands([
            \Modules\Xot\Console\CreateAllRepositoriesCommand::class,
            \Modules\Xot\Console\PanelMakeCommand::class,
            \Modules\Xot\Console\FixProvidersCommand::class,
        ]);

        if (false) {
            // --- meglio ficcare un controllo anche sull'env
            if (isset($_SERVER['SERVER_NAME']) && 'localhost' != $_SERVER['SERVER_NAME']
                && isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']
            ) {
                URL::forceScheme('https');
            }
        }
        //*
        $this->registerTranslator();
        $this->registerCacheOPCache();
        //*/
        resolve(EngineManager::class)->extend('fulltext', function () {
            return new FullTextSearchEngine();
        });
    }

    //end bootCallback

    public function mergeConfigs() {
        $configs = ['database', 'filesystems', 'auth', 'metatag', 'services', 'xra'];
        foreach ($configs as $v) {
            $tmp = tenantConfig($v);
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
    }

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
            $store = new Opcache\Store();

            return new Opcache\Repository($store, new TagSet($store));
        });
        // Extend Collection to implement __set_state magic method
        if (! Collection::hasMacro('__set_state')) {
            Collection::macro('__set_state', function (array $array) {
                return new Collection($array['items']);
            });
        }
    }
} //end class

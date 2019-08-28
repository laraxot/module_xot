<?php
namespace Modules\Xot\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

use Illuminate\Database\Eloquent\Relations\Relation; // per dizionario morph 


use Illuminate\Translation\Translator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


use Laravel\Scout\EngineManager; // per slegarmi da tntsearch
use Modules\Xot\Engines\FullTextSearchEngine;

use Modules\Xot\Services\TranslatorService;

use Modules\Xot\Providers\XotBaseServiceProvider;


class XotServiceProvider extends XotBaseServiceProvider{
    protected $module_dir= __DIR__;
    protected $module_ns=__NAMESPACE__;
    public $module_name='xot';  

    public function bootCallback(){
    	$this->mergeConfigs();
        if(\Request::has('act') && \Request::input('act')=='migrate'){
            DB::purge();
        }
        DB::reconnect();
    	$map=config('xra.model');
    	Relation::morphMap($map);
        $this->commands([
            \Modules\Xot\Console\CreateAllRepositoriesCommand::class,
        ]);

        if(false){
        // --- meglio ficcare un controllo anche sull'env
        if (isset($_SERVER['SERVER_NAME']) && 'localhost' != $_SERVER['SERVER_NAME']
            && isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']
        ) {
            URL::forceScheme('https');
        }
        }
       
        $this->registerTranslator();

        resolve(EngineManager::class)->extend('fulltext', function () {
            return new FullTextSearchEngine;
        });


    }//end bootCallback

    public function mergeConfigs(){
    	$configs=['database','filesystems','auth','metatag','services','xra'];
    	foreach($configs as $v){
    		$tmp=tenantConfig($v);
    		//ddd($tmp);
    	}
        //DB::purge('mysql');//Call to a member function prepare() on null
        //DB::purge('liveuser_general');
        //DB::reconnect();
    }//end mergeConfigs


    public function registerCallback(){
        $this->loadHelpersFrom(__DIR__.'/../Helpers');
    }

    public function loadHelpersFrom($path){
        foreach (\glob($path.'/*.php') as $filename) {
            $filename = \str_replace('/', \DIRECTORY_SEPARATOR, $filename);
            require_once $filename;
        }
    }

    public function registerTranslator(){
         // Override the JSON Translator
        $this->app->extend('translator', function (Translator $translator) {
            $trans = new TranslatorService($translator->getLoader(), $translator->getLocale());
            $trans->setFallback($translator->getFallback());
            return $trans;
        });
    }
    
}//end class

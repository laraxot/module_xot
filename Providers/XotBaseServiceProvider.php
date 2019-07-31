<?php
namespace Modules\Xot\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

use Modules;

abstract class XotBaseServiceProvider extends ServiceProvider{

	/**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(){
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom($this->module_dir . '/../Database/Migrations');
        if(method_exists($this, 'bootCallback')){
            $this->bootCallback();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(){
        //dd($this->module_name.' '.RouteServiceProvider::class);
        //dd(dirname(get_class($this))); //Modules\Backend\Providers\BackendServiceProvider
        //dd(__NAMESPACE__);
        //$ns=dirname(get_class($this));
        //dd(get_class($this).' '.$this->module_ns);
        $this->app->register(''.$this->module_ns.'\RouteServiceProvider');
        //get_called_class 
        //dd(get_class($this));
        if(method_exists($this, 'registerCallback')){
            $this->registerCallback();
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            $this->module_dir.'/../Config/config.php' => config_path($this->module_name.'.php'),
        ], 'config');
        $this->mergeConfigFrom(
            $this->module_dir.'/../Config/config.php', $this->module_name
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $sourcePath = realpath($this->module_dir.'/../Resources/views');
        /*
        $viewPath = resource_path('views/modules/'.$this->module_name);


        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/'.$this->module_name;
        }, \Config::get('view.paths')), [$sourcePath]), $this->module_name);
        */
        $this->loadViewsFrom($sourcePath,$this->module_name);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/'.$this->module_name);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->module_name);
        } else {
            $this->loadTranslationsFrom($this->module_dir .'/../Resources/lang', $this->module_name);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load($this->module_dir . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

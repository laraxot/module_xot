<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

//use Illuminate\Database\Eloquent\Factory;
use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

//use Modules;

/**
 * Class XotBaseServiceProvider.
 */
abstract class XotBaseServiceProvider extends ServiceProvider {
    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    public string $module_name = 'formx';

    protected string $module_base_ns;

    /**
     * Boot the application events.
     */
    public function boot(): void {
        //echo '<h3>Time :'.class_basename($this).' '.(microtime(true) - LARAVEL_START).'</h3>';

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom($this->module_dir.'/../Database/Migrations');
        if (method_exists($this, 'bootCallback')) {
            $this->bootCallback();
        }
        $this->registerLivewireComponents();
        $this->registerBladeComponents();
        //echo '<h3>Time :'.class_basename($this).' '.(microtime(true) - LARAVEL_START).'</h3>';
    }

    /**
     * Register the service provider.
     */
    public function register(): void {
        //dd($this->module_name.' '.RouteServiceProvider::class);
        //dd(dirname(get_class($this))); //Modules\Backend\Providers\BackendServiceProvider
        //dd(__NAMESPACE__);
        //$ns=dirname(get_class($this));
        //dd(get_class($this).' '.$this->module_ns);
        //echo '<h3>Time :'.class_basename($this).' '.(microtime(true) - LARAVEL_START).'</h3>';

        $this->app->register(''.$this->module_ns.'\RouteServiceProvider');
        //get_called_class
        //dd(get_class($this));
        if (method_exists($this, 'registerCallback')) {
            $this->registerCallback();
        }
        //echo '<h3>Time :'.class_basename($this).' '.(microtime(true) - LARAVEL_START).'</h3>';
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void {
        $this->publishes([
            $this->module_dir.'/../Config/config.php' => config_path($this->module_name.'.php'),
        ], 'config');
        $this->mergeConfigFrom(
            $this->module_dir.'/../Config/config.php',
            $this->module_name
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void {
        $sourcePath = realpath($this->module_dir.'/../Resources/views');
        if (false === $sourcePath) {
            throw new Exception('realpath not find dir');
        }
        /*
        $viewPath = resource_path('views/modules/'.$this->module_name);


        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/'.$this->module_name;
        }, \Config::get('view.paths')), [$sourcePath]), $this->module_name);
        */
        $this->loadViewsFrom($sourcePath, $this->module_name);
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void {
        $langPath = resource_path('lang/modules/'.$this->module_name);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->module_name);
        } else {
            $this->loadTranslationsFrom($this->module_dir.'/../Resources/lang', $this->module_name);
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void {
        if (! app()->environment('production')) {
            //app(Factory::class)->load($this->module_dir.'/../Database/factories');
        }
    }

    public function registerBladeComponents(): void {
        dddx(Module::find($this->module_name));
        Blade::componentNamespace('Modules\FormX\View\Components', $this->module_name);
    }

    public function registerLivewireComponents(): void {
        $components_json = $this->module_dir.'/../Http/Livewire/_components.json';
        //$force_recreate = request()->input('force_recreate', true);
        $exists = File::exists($components_json);
        if ($exists) {
            $content = File::get($components_json);
            $comps = json_decode($content);
        } else {
            $files = File::allFiles(dirname($components_json));

            $comps = [];
            foreach ($files as $k => $v) {
                if ('php' == $v->getExtension()) {
                    $tmp = (object) [];
                    $class_name = Str::before($v->getBasename(), '.php');

                    $tmp->class_name = $class_name;
                    $tmp->comp_name = $this->module_name.'::'.Str::snake($class_name);
                    $tmp->comp_ns = Str::before($this->module_ns, '\Providers').'\Http\Livewire\\'.$class_name;
                    if ('' != $v->getRelativePath()) {
                        $tmp->comp_name = $this->module_name.'::'.Str::snake($v->getRelativePath()).'.'.Str::snake($class_name);
                        $tmp->comp_ns = Str::before($this->module_ns, '\Providers').'\Http\Livewire\\'.$v->getRelativePath().'\\'.$class_name;
                    }

                    $comps[] = $tmp;
                }
            }

            //dddx([$comps, $components_json]);
            $content = json_encode($comps);
            if (false === $content) {
                throw new Exception('can not decode json');
            }
            $old_content = '';
            if (File::exists($components_json)) {
                $old_content = File::get($components_json);
            }
            if ($old_content != $content) {
                File::put($components_json, $content);
            }
        }
        //dddx($comps);
        if (class_exists("Livewire\Livewire")) {
            foreach ($comps as $comp) {
                \Livewire\Livewire::component($comp->comp_name, $comp->comp_ns);
            }
            //Livewire::component($this->module_name.'::calendar', Calendar::class);
            //Livewire::component($this->module_name.'::numberer', Numberer::class);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadEventsFrom(string $path): void {
        $events = [];
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $events_file = $path.'/_events.json';
        $force_recreate = request()->input('force_recreate', true);
        if (! File::exists($events_file) || $force_recreate) {
            foreach (\glob($path.'/*.php') as $filename) {
                $info = pathinfo($filename);

                //$tmp->namespace='\\'.$vendor.'\\'.$pack.'\\Events\\'.$info['filename'];
                $event_name = $info['filename'];
                $str = 'Event';
                if (Str::endsWith($event_name, $str)) {
                    $listener_name = substr($event_name, 0, -strlen($str)).'Listener';

                    $event = $this->module_base_ns.'\\Events\\'.$event_name;
                    $listener = $this->module_base_ns.'\\Listeners\\'.$listener_name;
                    $msg = [
                        'event' => $event,
                        'event_exists' => class_exists($event),
                        'listener' => $listener,
                        'listener_exists' => class_exists($listener),
                    ];
                    if (class_exists($event) && class_exists($listener)) {
                        //\Event::listen($event, $listener);
                        $tmp = new \StdClass();
                        $tmp->event = $event;
                        $tmp->listener = $listener;
                        $events[] = $tmp;
                    }
                }
            }
            try {
                $events_content = json_encode($events);
                if (false === $events_content) {
                    throw new Exception('can not encode json');
                }
                File::put($events_file, $events_content);
            } catch (\Exception $e) {
                dd($e);
            }
        } else {
            $events = File::get($events_file);
            $events = json_decode($events);
        }
        foreach ($events as $v) {
            Event::listen($v->event, $v->listener);
        }
    }

    //end function
}
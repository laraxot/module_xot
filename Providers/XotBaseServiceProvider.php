<?php

declare(strict_types=1);

namespace Modules\Xot\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Modules\Xot\Services\BladeService;
use Modules\Xot\Services\LivewireService;
use Nwidart\Modules\Facades\Module;

use function Safe\glob;
use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\realpath;

// use Modules;

/**
 * Class XotBaseServiceProvider.
 */
abstract class XotBaseServiceProvider extends ServiceProvider
{
    public string $module_name = 'xot';
    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    protected string $module_base_ns;

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        // $this->registerFactories();
        $this->loadMigrationsFrom($this->module_dir.'/../Database/Migrations');
        if (method_exists($this, 'bootCallback')) {
            $this->bootCallback();
        }
        // Illuminate\Contracts\Container\BindingResolutionException: Target class [livewire] does not exist.
        $this->registerLivewireComponents();
        // Illuminate\Contracts\Container\BindingResolutionException: Target class [modules] does not exist.
        $this->registerBladeComponents();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->module_ns = collect(explode('\\', $this->module_ns))->slice(0, -1)->implode('\\');
        $this->app->register(''.$this->module_ns.'\Providers\RouteServiceProvider');
        if (method_exists($this, 'registerCallback')) {
            $this->registerCallback();
        }
        // echo '<h3>Time :'.class_basename($this).' '.(microtime(true) - LARAVEL_START).'</h3>';
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $sourcePath = realpath($this->module_dir.'/../Resources/views');
        // if (false === $sourcePath) {
        //    throw new \Exception('realpath not find dir');
        // }
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
    public function registerTranslations(): void
    {
        $langPath = realpath($this->module_dir.'/../Resources/lang');
        // if (false === $langPath) {
        //    throw new \Exception('['.__LINE__.']['.__FILE__.']');
        // }
        // echo '<hr>'.$langPath.'  :  '.$this->module_name.' <hr/>';
        $this->loadTranslationsFrom($langPath, $this->module_name);
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (! app()->environment('production')) {
            // app(Factory::class)->load($this->module_dir.'/../Database/factories');
        }
    }

    public function registerBladeComponents(): void
    {
        /*
        $module = Module::find($this->module_name);
        if (null == $module) {
            throw new \Exception('['.$this->module_name.'] is not found');
        }

        $namespace = 'Modules\\'.$module->getName().'\View\Components';

        Blade::componentNamespace($namespace, $module->getLowerName());
        */
        $namespace = $this->module_ns.'\View\Components';
        Blade::componentNamespace($namespace, $this->module_name);
        /*
        dddx([
            'module_ns'=>$this->module_ns,
            'module_dir'=>$this->module_dir,
            'this'=>$this,
        ])
        ;
        */
        BladeService::registerComponents($this->module_dir.'/../View/Components', $this->module_ns);
    }

    /**
     * Undocumented function.
     */
    public function registerLivewireComponents(): void
    {
        // $prefix=$this->module_name.'::';
        $prefix = '';
        LivewireService::registerComponents(
            $this->module_dir.'/../Http/Livewire',
            Str::before($this->module_ns, '\Providers'),
            $prefix,
        );
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

    /**
     * Undocumented function.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getEventsFrom(string $path): array
    {
        $events = [];
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $events_file = $path.'/_events.json';
        $force_recreate = request()->input('force_recreate', true);
        if (! File::exists($events_file) || $force_recreate) {
            $filenames = glob($path.'/*.php');
            // if (false === $filenames) {
            //    $filenames = [];
            // }
            foreach ($filenames as $filename) {
                $info = pathinfo($filename);

                // $tmp->namespace='\\'.$vendor.'\\'.$pack.'\\Events\\'.$info['filename'];
                $event_name = $info['filename'];
                $str = 'Event';
                if (Str::endsWith($event_name, $str)) {
                    $listener_name = substr($event_name, 0, -\strlen($str)).'Listener';

                    $event = $this->module_base_ns.'\\Events\\'.$event_name;
                    $listener = $this->module_base_ns.'\\Listeners\\'.$listener_name;
                    $msg = [
                        'event' => $event,
                        'event_exists' => class_exists($event),
                        'listener' => $listener,
                        'listener_exists' => class_exists($listener),
                    ];
                    if (class_exists($event) && class_exists($listener)) {
                        // \Event::listen($event, $listener);
                        $tmp = new \stdClass();
                        $tmp->event = $event;
                        $tmp->listener = $listener;
                        $events[] = $tmp;
                    }
                }
            }
            try {
                $events_content = json_encode($events);
                // if (false === $events_content) {
                //    throw new \Exception('can not encode json');
                // }
                File::put($events_file, $events_content);
            } catch (\Exception $e) {
                dd($e);
            }
        } else {
            $events = File::get($events_file);
            $events = (array) json_decode($events);
        }

        return $events;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadEventsFrom(string $path): void
    {
        $events = $this->getEventsFrom($path);
        foreach ($events as $v) {
            Event::listen($v->event, $v->listener);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        /*
        $this->publishes(
            [
                $this->module_dir.'/../Config/config.php' => config_path($this->module_name.'.php'),
            ],
            'config'
        );
        */
        $this->mergeConfigFrom(
            $this->module_dir.'/../Config/config.php',
            $this->module_name
        );
    }

    // end function
}

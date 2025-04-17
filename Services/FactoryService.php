<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Class FactoryService.
 * https://github.com/TheDoctor0/laravel-factory-generator. 24 days ago
 * https://github.com/mpociot/laravel-test-factory-helper  on 2 Mar 2020.
 * https://github.com/laravel-shift/factory-generator on 10 Aug.
 * https://dev.to/marcosgad/make-factory-more-organized-laravel-3c19.
 * https://medium.com/@yohan7788/seeders-and-faker-in-laravel-6806084a0c7.
 */
class FactoryService
{
    /**
     * Create a new factory instance for the model.
     */
    public static function newFactory(string $called_class): Factory
    {
        /*
        $model_name = class_basename($called_class);
        $module_name = Str::between($called_class, 'Modules\\', '\\Models\\');

        $factory_class = Str::replace('\Models\\', '\Database\Factories\\', $called_class).'Factory';

        if (class_exists($factory_class)) {
            return $factory_class::new();
        }

        $res = Artisan::call('module:make-factory', ['name' => $model_name, 'module' => $module_name]);
        //*/

        $factory_class = StubService::make()->setModelClass($called_class)
            ->setName('factory')
            ->get();

        if (class_exists($factory_class)) {
            return $factory_class::new();
        }

        throw new \Exception('Generating Factory ['.$factory_class.'] press [F5] to refresh page ['.__LINE__.']['.__FILE__.']');
        // per ora è lasciato come prima
    }
}

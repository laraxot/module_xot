<?php

namespace Modules\Xot\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Xot\Console\GenerateFactoryCommand;

class XotFactoryGeneratorProvider extends XotBaseServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

       // $viewPath = __DIR__ . '/../Resources/views';

       $viewPath = '../Resources/views/factory-generator';

        $this->loadViewsFrom($viewPath, 'factory-generator');

        $this->commands([GenerateFactoryCommand::class]);
    }
}
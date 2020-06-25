<?php

namespace Modules\Xot\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class PanelMakeCommand extends GeneratorCommand {
    use ModuleCommandTrait;

    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xot:make-panel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new panel class for the specified module';

    public function getTemplateContents() {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return 'testinaaaa';
        /*
        return (new Stub('/testina.stub', [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getClass(),
        ]))->render();
        */
    }

    public function getDestinationFilePath() {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $panelPath = GenerateConfigReader::read('panel');

        return $path.$panelPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    protected function getFileName() {
        return Str::studly($this->argument('name'));
    }

    public function getDefaultNamespace(): string {
        return $this->laravel['modules']->config('paths.generator.panel.path', 'panels');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the panel.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }
}

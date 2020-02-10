<?php

namespace Modules\Xot\Console;

use Illuminate\Console\Command;
//use Illuminate\Console\GeneratorCommand;
//use Nwidart\Modules\Commands\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class FixProvidersCommand extends Command {
    protected $name = 'xot:fix-module-providers';
    protected $description = 'fix module providers of module ';
    //protected $type = 'ModuleProvider';
    //protected $argumentName='name';

    /*
    protected function getStub(){
        return __DIR__.'/stubs/module_provider.stub';
    }
    protected function getTemplateContents(){
    	$stub=File::get($this->getStub());
    	$module_name =$this->argument('name');
        $module = \Module::findOrFail($module_name);
        $module_ns=$this->laravel['modules']->config('namespace');
        $replaces=[
        	'DummyNamespace'	=> $module_ns.'\\'.$module->getStudlyName().'\Providers',
        	'DummyClass'		=> $module->getStudlyName().'Provider',
        	'dummy_name'		=> $module->getLowerName(),
    	];
    	$stub=str_replace(array_keys($replaces),array_values($replaces),$stub);
    	return $stub;
    }

    public function getDestinationFilePath(){
        $module_name =$this->argument('name');
        $module = \Module::findOrFail($module_name);
        $providers_path=realpath($module->getExtraPath('Providers'));
        $module_name =$module->getStudlyName();
        return $providers_path.'/'.$module_name.'ServiceProvider_1.php';
    }
    */
    /*
    protected function getNamespace($name){
        //dd($rootNamespace);

        //$module = \Module::findOrFail($name);
        return 'Modules\Mazinga\Providers';
    }
    //*/

    /*
    protected function qualifyClass($name){
        //return 'Modules\Mazinga';
        $module = \Module::findOrFail($name);
        return $module->getStudlyName().'Provider';
    }


    protected function getPath($name){
        $module = \Module::findOrFail(Str::before($name,'Provider'));
        return $module->getExtraPath('Providers\test4.test');
    }
    */
    protected function getArguments() {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module.'],
        ];
    }

    /*


    public function handle() {


        $module_name = $this->ask('What is the name of Module ?');
        $module = \Module::findOrFail($module_name);
        $providers_path=realpath($module->getExtraPath('Providers'));
        $module_name =$module->getStudlyName();
        $route_provider_stub=File::get(__DIR__.'/stubs/route_provider.stub');
        $module_provider_stub=File::get(__DIR__.'/stubs/module_provider.stub');
        $replaces=[
            'DummyNamespace'=>$this->getClassNamespace($module),
            'DummyClass'=>$module_name.'Provider',
        ];
        dd($replaces);
    }
    */
    /*
    protected function getDefaultNamespace($rootNamespace){
        $arg=$this->argument($this->argumentName);
        $module = \Module::findOrFail($arg);
        return $this->getClassNamespace($module).'\Providers';
    }
    */

    public function handle() {
        $module_name = $this->argument('name');
        $module = \Module::findOrFail($module_name);
        $providers_path = realpath($module->getExtraPath('Providers'));
        $module_name = $module->getStudlyName();
        $route_provider_stub = File::get(__DIR__.'/stubs/route_provider.stub');
        $module_provider_stub = File::get(__DIR__.'/stubs/module_provider.stub');
        $module_ns = $this->laravel['modules']->config('namespace');
        $dummy_class = $module_name.'ServiceProvider';
        $replaces = [
            'DummyNamespace' => $module_ns.'\\'.$module_name.'\Providers',
            'DummyClass' => $dummy_class,
            'dummy_name' => $module->getLowerName(),
            'DummyControllerNamespace' => $module_ns.'\\'.$module_name.'\Http\Controllers',
        ];

        $module_provider_stub = str_replace(array_keys($replaces), array_values($replaces), $module_provider_stub);
        $filename = $providers_path.'\\'.$dummy_class.'.php';
        if (File::exists($filename)) {
            $this->info($filename.' already exists!');
            $tmp = File::get($filename);
            if (Str::contains($tmp, 'XotBaseServiceProvider')) {
                $this->error($filename.' just fixed!');
            //return false;
            } else {
                File::put($filename, $module_provider_stub);
            }
        } else {
            File::put($filename, $module_provider_stub);
        }

        $dummy_class = 'RouteServiceProvider';
        $replaces['DummyClass'] = $dummy_class;
        $route_provider_stub = str_replace(array_keys($replaces), array_values($replaces), $route_provider_stub);
        $filename = $providers_path.'\\'.$dummy_class.'.php';
        if (File::exists($filename)) {
            $this->info($filename.' already exists!');
            $tmp = File::get($filename);
            if (Str::contains($tmp, 'XotBaseRouteServiceProvider')) {
                $this->error($filename.' just fixed!');
            } else {
                File::put($filename, $route_provider_stub);
            }
        } else {
            File::put($filename, $route_provider_stub);
        }

        $img_path = $module->getExtraPath('Resources/views/img/icon.png');
        if (! File::exists(dirname($img_path))) {
            try {
                File::makeDirectory(dirname($img_path), 0755, true, true);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (! File::exists($img_path)) {
            File::copy(public_path('img/icon.png'), $img_path);
        }
    }
}

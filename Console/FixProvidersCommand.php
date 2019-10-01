<?php
namespace Modules\Xot\Console;


//use Illuminate\Console\Command;
//use Illuminate\Console\GeneratorCommand;
use Nwidart\Modules\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class FixProvidersCommand extends GeneratorCommand {
    protected $name = 'xot:fix-module-provider';
    protected $description = 'fix module provider of module ';
    //protected $type = 'ModuleProvider';
    //protected $argumentName='name';

    protected function getStub(){
        return __DIR__.'/stubs/module_provider.stub';
    }

    protected function getTemplateContents(){
    	$stub=File::get($this->getStub());
    	$module_name =$this->argument('module_name');
        $module = \Module::findOrFail($module_name);
        $module_ns=$this->laravel['modules']->config('namespace');
        //Modules\Theme\Providers DummyNamespace
        $replaces=[
        	'DummyNamespace'	=> $module_ns.'\\'.$module->getStudlyName().'\Providers',
        	'DummyClass'		=> $module->getStudlyName().'Provider',
        	'dummy_name'		=> $module->getLowerName(),
    		//'MODULENAME'        => $module->getStudlyName(),
    		//'NAMESPACE'         => $module->getStudlyName(),
    		//'LOWER_NAME'        => $module->getLowerName(),
    		//'STUDLY_NAME'       => $module->getStudlyName(),
    		//'MODULE_NAMESPACE'  => $module_ns,
    	];
    	$stub=str_replace(array_keys($replaces),array_values($replaces),$stub);
    	return $stub;
    }

    public function getDestinationFilePath(){
    	/*
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());
        $controllerPath = GenerateConfigReader::read('provider');

        return $path . $controllerPath->getPath() . '/' . $this->getControllerName() . '.test';
        */
        $module_name =$this->argument('module_name');
        $module = \Module::findOrFail($module_name);
        $providers_path=realpath($module->getExtraPath('Providers'));
        $module_name =$module->getStudlyName();
        return $providers_path.'/'.$module_name.'ServiceProvider_1.php';
    }

    protected function getArguments(){
        return [
        	 ['module_name', InputArgument::REQUIRED, 'The name of the module.'],
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
    


}

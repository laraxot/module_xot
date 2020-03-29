<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PanelService {
    private static $_instance = null;
    private static $model;
    private static $panel;

    /*
    public function __construct($model){
    $this->model=$model;
    }
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function get($model) {
        return self::setModel($model)->panel();
    }

    public static function setModel($model) {
        self::$model = $model;

        return self::getInstance();
    }

    public static function panel() {
    	if(!is_object(self::$model)){
    		ddd('wip');
    	}
        $class_full = get_class(self::$model);
        $class_name = class_basename(self::$model);
        //$class = Str::before($class_full, $class_name);
        $class = substr($class_full, 0, -strlen($class_name));
        $panel_class = $class.'Panels\\'.$class_name.'Panel';
        if (! class_exists($panel_class)) {
            $tmp = StubService::getByModel(self::$model, 'panel', $create = true);
        }
        self::$panel = new $panel_class(self::$model);

        return self::$panel;
    }

    public function imageHtml($params) {
        return self::$model->image_src;
    }

    public function tabs() {
        return self::panel()->tabs();
    }

    public static function getByModel($model) {
        $class_full = get_class($model);
        $class_name = class_basename($model);
        $class = Str::before($class_full, $class_name);
        $panel = $class.'Panels\\'.$class_name.'Panel';
        if (class_exists($panel)) {
            if (! method_exists($panel, 'tabs')) {
                self::updatePanel(['panel' => $panel, 'func' => 'tabs']);
            }

            return new $panel();
        }
        self::createPanel($model);
        \Session::flash('status', 'panel created');

        return redirect()->back();
    }

    public static function createPanel($model) {
        if(!is_object($model)){
            ddd('da fare');
        }
        $class_full = get_class($model);
        $class_name = class_basename($model);
        $class = Str::before($class_full, $class_name);
        $panel_namespace = $class.'Panels';
        $panel = $panel_namespace.'\\'.$class_name.'Panel';
        //---- creazione panel
        $autoloader_reflector = new \ReflectionClass($model);
        $class_file_nanme = $autoloader_reflector->getFileName();
        $model_dir = dirname($class_file_nanme); // /home/vagrant/code/htdocs/lara/multi/laravel/Modules/LU/Models
        $stub_file = __DIR__.'/../Console/stubs/panel.stub';
        $stub = File::get($stub_file);
        $search = [];
        $fillables = $model->getFillable();
        $fields = [];
        foreach ($fillables as $input_name) {
            try {
                $input_type = $model->getConnection()->getDoctrineColumn($model->getTable(), $input_name)->getType(); //->getName();
            } catch (\Exception $e) {
                $input_type = 'Text';
            }
            $tmp = new \stdClass();
            $tmp->type = (string) $input_type;
            $tmp->name = $input_name;
            $fields[] = $tmp;
        }
        $dummy_id = $model->getRouteKeyName();
        if (is_array($dummy_id)) {
            echo '<h3>not work with multiple keys</h3>';
            $dummy_id = var_export($dummy_id, true);
        }
        $replace = [
            'DummyNamespace' => $panel_namespace,
            'DummyClass' => $class_name.'Panel',
            'DummyFullModel' => $class_full,
            'dummy_id' => $dummy_id,
            'dummy_title' => 'title', // prendo il primo campo stringa
            'dummy_search' => var_export($search, true),
            'dummy_fields' => var_export($fields, true),
        ];
        $stub = str_replace(array_keys($replace), array_values($replace), $stub);
        $panel_dir = $model_dir.'/Panels';
        File::makeDirectory($panel_dir, $mode = 0777, true, true);
        $panel_file = $panel_dir.'/'.$class_name.'Panel.php';
        File::put($panel_file, $stub);
    }

    public static function updatePanel($params) {
        extract($params);
        $func_file = __DIR__.'/../Console/stubs/panels/'.$func.'.stub';
        $func_stub = File::get($func_file);
        $autoloader_reflector = new \ReflectionClass($panel);
        $panel_file = $autoloader_reflector->getFileName();
        $panel_stub = File::get($panel_file);
        $panel_stub = Str::replaceLast('}', $func_stub.chr(13).chr(10).'}', $panel_stub);
        File::put($panel_file, $panel_stub);
    }
}

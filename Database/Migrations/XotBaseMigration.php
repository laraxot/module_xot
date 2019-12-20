<?php

namespace Modules\Xot\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

//----- models -----
//use Modules\Customer\Models\Customer as MyModel;
//use Modules\Blog\Models\Place;

class XotBaseMigration extends Migration {
    private $model;

    public function __construct() {
        //ddd(class_basename($this));//CreateDevicesTable
        //ddd(get_class($this));
        $name = class_basename($this);
        $name = Str::before(Str::after($name, 'Create'), 'Table');
        $name = Str::singular($name);
        //ddd($name);//Device
        $reflection_class = new \ReflectionClass($this);
        $filename = $reflection_class->getFilename();
        //ddd($filename);//C:\var\www\multi\laravel\Modules\Customer\Database\Migrations\2019_12_11_082626_create_devices_table.php
        $mod_path = \Module::getPath();
        //ddd($mod_path);//C:\var\www\multi\laravel\Modules
        $mod_name = Str::after($filename, $mod_path);
        $mod_name = explode(DIRECTORY_SEPARATOR, $mod_name)[1];

        $model_ns = '\Modules\\'.$mod_name.'\Models\\'.$name;
        $model_dir = $mod_path.DIRECTORY_SEPARATOR.$mod_name.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.$name.'.php';
        //ddd($model_ns);//     \Modules\Customer\Models\Device
        //ddd($model_dir);//    C:\var\www\multi\laravel\Modules\Customer\Models\Device.php
        $model = new $model_ns();
        //ddd($model->getTable());
        $this->model = $model;
    }

    public function getTable() {
        //return with(new MyModel())->getTable();
        return $this->model->getTable();
    }

    public function getConn() {
        //$conn_name=with(new MyModel())->getConnectionName();
        $conn_name = $this->model->getConnectionName();
        $conn = Schema::connection($conn_name);

        return $conn;
    }

    public function tableExists() {
        return $this->getConn()->hasTable($this->getTable());
    }

    public function hasColumn($col) {
        return $this->getConn()->hasColumn($this->getTable(), $col);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $this->getConn()->dropIfExists($this->getTable());
    }
}//end XotBaseMigration

<?php

declare(strict_types=1);

namespace Modules\Xot\Database\Migrations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

//----- models -----

/**
 * Class XotBaseMigration.
 */
abstract class XotBaseMigration extends Migration {
    protected ?Model $model = null;

    //*
    public function __construct() {
        if (null == $this->model) {
            $this->model = app($this->getModel());
        }
        //$this->model = new $this->model();
    }

    //*/

    public function getModel(): string {
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
        //$model = new $model_ns();
        //return $model;
        //ddd($model->getTable());
        return $model_ns;
    }

    public function getTable(): string {
        $table = $this->model->getTable();
        /*
        if (Str::endsWith($table, '_pivot')) {
            $table = Str::before($table, '_pivot');
        }
        */
        return $table;
    }

    /**
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getConn() {
        //$conn_name=with(new MyModel())->getConnectionName();
        //\DB::reconnect('mysql');
        //dddx(config('database'));
        //\DB::purge('mysql');
        //\DB::reconnect('mysql');
        $conn_name = $this->model->getConnectionName();
        $conn = Schema::connection($conn_name);

        return $conn;
    }

    /**
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public function getSchemaManager() {
        $schema_manager = $this->getConn()
            ->getConnection()
            ->getDoctrineSchemaManager();

        return $schema_manager;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function getTableDetails() {
        $table_details = $this->getSchemaManager()
            ->listTableDetails($this->getTable());

        return $table_details;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Doctrine\DBAL\Schema\Index[]
     */
    public function getTableIndexes() {
        $table_indexes = $this->getSchemaManager()
            ->listTableIndexes($this->getTable());

        return $table_indexes;
    }

    /**
     * @return bool
     */
    public function tableExists() {
        return $this->getConn()->hasTable($this->getTable());
    }

    /**
     * @param string $col
     *
     * @return bool
     */
    public function hasColumn($col) {
        return $this->getConn()->hasColumn($this->getTable(), $col);
    }

    /**
     * @param string $sql
     */
    public function query($sql) {
        $this->getConn()->getConnection()->statement($sql);
    }

    /**
     * @return bool
     */
    public function hasPrimaryKey() {
        $table_details = $this->getTableDetails();

        return $table_details->hasPrimaryKey();
    }

    public function dropPrimaryKey() {
        $table_details = $this->getTableDetails();
        $table_details->dropPrimaryKey();
        $sql = 'ALTER TABLE '.$this->getTable().' DROP PRIMARY KEY;';
        $this->query($sql);
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

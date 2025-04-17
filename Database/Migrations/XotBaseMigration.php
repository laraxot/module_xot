<?php

declare(strict_types=1);

namespace Modules\Xot\Database\Migrations;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Xot\Services\StubService;
use Nwidart\Modules\Facades\Module;

// ----- models -----

/**
 * Class XotBaseMigration.
 */
abstract class XotBaseMigration extends Migration
{
    protected ?Model $model = null;

    protected ?string $model_class = null;

    // *
    public function __construct()
    {
        if (null === $this->model) {
            $model = $this->getModel();
            // 37     Dead catch - Exception is never thrown in the try block.
            // try {
            $this->model = app($model);
            // } catch (\Exception $ex) {
            //    $res = StubService::make()->setModelClass($model)->setName('model')->get();
            //    throw new \Exception('<br><br>Table '.get_class($this).' does not have model '.$model.'<br><br>');
            // }
        }
        // $this->model = new $this->model();
    }

    // */

    public function getModel(): string
    {
        if (null !== $this->model_class) {
            return $this->model_class;
        }
        $name = class_basename($this);
        $name = Str::before(Str::after($name, 'Create'), 'Table');
        $name = Str::singular($name);
        $reflection_class = new \ReflectionClass($this);
        $filename = (string) $reflection_class->getFilename();
        $mod_path = Module::getPath();

        $mod_name = Str::after($filename, $mod_path);
        $mod_name = explode(\DIRECTORY_SEPARATOR, $mod_name)[1];

        $model_ns = '\Modules\\'.$mod_name.'\Models\\'.$name;
        $model_dir = $mod_path.'/'.$mod_name.'/Models/'.$name.'.php';
        $model_dir = Str::replace('/', \DIRECTORY_SEPARATOR, $model_dir);

        return $model_ns;
    }

    public function getTable(): string
    {
        if (null === $this->model) {
            return '';
        }
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
    public function getConn()
    {
        // $conn_name=with(new MyModel())->getConnectionName();
        // \DB::reconnect('mysql');
        // dddx(config('database'));
        // \DB::purge('mysql');
        // \DB::reconnect('mysql');
        if (null === $this->model) {
            throw new \Exception('model is null');
        }

        $conn_name = $this->model->getConnectionName();

        // dddx([$this->model, $conn_name]);
        $conn = Schema::connection($conn_name);

        return $conn;
    }

    /**
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public function getSchemaManager()
    {
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
    public function getTableDetails()
    {
        $table_details = $this->getSchemaManager()
            ->listTableDetails($this->getTable());

        return $table_details;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Doctrine\DBAL\Schema\Index[]
     */
    public function getTableIndexes()
    {
        $table_indexes = $this->getSchemaManager()
            ->listTableIndexes($this->getTable());

        return $table_indexes;
    }

    /**
     * ---.
     */
    public function tableExists(string $table = null): bool
    {
        if (null === $table) {
            $table = $this->getTable();
        }

        return $this->getConn()->hasTable($table);
    }

    public function hasColumn(string $col): bool
    {
        return $this->getConn()->hasColumn($this->getTable(), $col);
    }

    /**
     * Get the data type for the given column name.
     */
    public function getColumnType(string $column): string
    {
        return $this->getConn()->getColumnType($this->getTable(), $column);
    }

    /**
     * Undocumented function.
     */
    public function isColumnType(string $column, string $type): bool
    {
        if (! $this->hasColumn($column)) {
            return false;
        }

        return $this->getColumnType($column) === $type;
    }

    /**
     * ---.
     */
    public function query(string $sql): void
    {
        $this->getConn()->getConnection()->statement($sql);
    }

    public function hasIndex(string $index): bool
    {
        /*
        $tbl = $this->getTable();
        $conn = $this->getConn()->getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($tbl);
        */
        $tbl = $this->getTable();
        $doctrineTable = $this->getTableDetails();

        // $indexes=$this->getTableIndexes();
        $has_index = $doctrineTable->hasIndex($tbl.'_'.$index.'_index');
        // dddx(['indexes'=>$indexes,'has_index'=>$has_index]);
        return $has_index;
    }

    public function hasIndexName(string $name): bool
    {
        $doctrineTable = $this->getTableDetails();
        $has_index = $doctrineTable->hasIndex($name);

        return $has_index;
    }

    /**
     * ---.
     */
    public function hasPrimaryKey(): bool
    {
        $table_details = $this->getTableDetails();

        return $table_details->hasPrimaryKey();
    }

    public function dropPrimaryKey(): void
    {
        $table_details = $this->getTableDetails();
        $table_details->dropPrimaryKey();
        $sql = 'ALTER TABLE '.$this->getTable().' DROP PRIMARY KEY;';
        $this->query($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @return void
     */
    public function down()
    {
        $this->getConn()->dropIfExists($this->getTable());
    }

    public function tableDrop(string $table): void
    {
        $this->getConn()->dropIfExists($table);
    }

    public function rename(string $from, string $to): void
    {
        $this->getConn()->rename($from, $to);
    }

    public function renameTable(string $from, string $to): void
    {
        if ($this->tableExists($from)) {
            $this->getConn()->rename($from, $to);
        }
    }

    // da rivedere
    public function renameColumn(string $from, string $to): void
    {
        // Call to an undefined method Illuminate\Database\Schema\Builder::renameColumn().
        /**
         * @var \Illuminate\Database\Schema\Blueprint
         */
        $conn = $this->getConn();
        $conn->renameColumn($from, $to);
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    public function tableCreate(\Closure $next)
    {
        if (! $this->tableExists()) {
            $this->getConn()->create(
                $this->getTable(),
                $next
            );
        }
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    public function tableUpdate(\Closure $next)
    {
        $this->getConn()->table(
            $this->getTable(),
            $next
        );
    }
}// end XotBaseMigration

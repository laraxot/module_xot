<?php

/**
 * @see https:// github.com/spatie/laravel-model-info
 * @see https://freek.dev/2332-getting-information-about-all-the-models-in-your-laravel-app
 */

declare(strict_types=1);

namespace Modules\Xot\Services;

// ----------- Requests ----------
use function get_class;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use function in_array;

// per dizionario morph
// ------------ services ----------

/**
 * Class ModelService.
 */
class ModelService
{
    private static ?self $_instance = null;

    protected Model $model;

    /**
     * getInstance.
     *
     * this method will return instance of the class
     */
    public static function getInstance(): self
    {
        if (! self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Undocumented function.
     */
    public static function make(): self
    {
        return static::getInstance();
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setModelClass(string $class): self
    {
        $this->model = app($class);

        return $this;
    }

    /**
     * Undocumented function.
     */
    public function getRelationshipsAndData(array $data): array
    {
        $model = $this->model;
        $methods = get_class_methods($model);

        // $post_type = $this->getPostType();
        // Relation::morphMap([$post_type => get_class($model)]);
        $data = collect($data)->filter(
            function ($item, $key) use ($methods) {
                return \in_array($key, $methods, true);
            }
        )->map(
            function ($v, $k) use ($model, $data) {
                if (! \is_string($k)) {
                    dddx([$k, $v, $data]);
                }
                $rows = $model->$k();
                $related = null;
                if (\is_object($rows) && method_exists($rows, 'getRelated')) {
                    $related = $rows->getRelated();
                }

                return (object) [
                    'relationship_type' => class_basename($rows),
                    'is_relation' => $rows instanceof Relation,
                    'related' => $related,
                    'data' => $v,
                    'name' => $k,
                    'rows' => $rows,
                ];
            }
        )
            ->filter(
                function ($item) {
                    return $item->is_relation;
                }
            )
            ->all();

        return $data;
    }

    public function getPostType(): string
    {
        $model = $this->model;
        // da trovare la funzione che fa l'inverso
        // static string|null getMorphedModel(string $alias) Get the model associated with a custom polymorphic type.
        // static array morphMap(array $map = null, bool $merge = true) Set or get the morph map for polymorphic relations.
        /**
         * @var array
         */
        $models = config('morph_map');

        $post_type = collect($models)->search(\get_class($model));

        if (false === $post_type) {
            $post_type = snake_case(class_basename($model));
            Relation::morphMap([$post_type => \get_class($model)]);
        }

        return (string) $post_type;
    }

    /**
     * Undocumented function
     * funziona leggendo o il "commento" prima della funzione o quello che si dichiara come returnType.
     */
    public function getRelations(): array
    {
        $model = $this->model;
        $reflector = new \ReflectionClass($model);
        $relations = [];
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $doc = $method->getDocComment();

            $res = $method->getName(); // ?? $method->__toString(); // 76     Call to an undefined method ReflectionType::getName().
            // $res = PHP_VERSION_ID < 70100 ? $method->__toString() : $method->getName();

            if (0 === $method->getNumberOfRequiredParameters() && $method->class === \get_class($model)) {
                // $returnType = $method->getReturnType();
                // if (null !== $returnType && false !== strpos($returnType->getName(), '\\Relations\\')) {
                // if (in_array(class_basename($returnType->getName()), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
                //    $relations[] = $res;
                // } elseif ($doc && false !== strpos($doc, '\\Relations\\')) {
                if ($doc && false !== strpos($doc, '\\Relations\\')) {
                    $relations[] = $res;
                }
            }
        }

        return $relations;
    }

    /**
     * Undocumented function
     * questa funzione va ad esequire e prende il risultato, buona per controllare le 2 funzioni che devono dare lo stesso numero, questa funzione molto piu' lenta (da controllare).
     *
     *              https://laracasts.com/discuss/channels/eloquent/get-all-model-relationships.
     */
    public function getRelationships(): array
    {
        $model = $this->model;
        $relationships = [];

        foreach ((new \ReflectionClass($model))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== \get_class($model)
                || ! empty($method->getParameters())
                || __FUNCTION__ === $method->getName()
            ) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof Relation) {
                    $relationships[$method->getName()] = [
                        'name' => $method->getName(),
                        'type' => (new \ReflectionClass($return))->getShortName(),
                        'model' => (new \ReflectionClass($return->getRelated()))->getName(),
                    ];
                }
            } catch (\ErrorException $e) {
            }
        }

        return $relationships;
    }

    public function getNameRelationships(): array
    {
        $model = $this->model;
        $relations = self::getRelationships();
        $names = collect($relations)->map(
            function ($item) {
                return $item['name'];
            }
        )->values()->all();

        return $names;
    }

    /**
     * @param array|string $index
     */
    public function indexIfNotExists($index): void
    {
        $model = $this->model;
        if (\is_array($index)) {
            foreach ($index as $i) {
                $this->indexIfNotExists($i);
            }
        } else {
            $tbl = $model->getTable();
            $conn = $model->getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable = $dbSchemaManager->listTableDetails($tbl);
            // faremo dei controlli per non aggiungere troppe chiavi
            if (! $doctrineTable->hasIndex($tbl.'_'.$index.'_index')) {
                Schema::connection($conn->getName())->table(
                    $tbl,
                    function ($table) use ($index): void {
                        $table->index($index);
                    }
                );
            }
        }
    }

    public function fieldExists(string $field_name): bool
    {
        $model = $this->model;

        return \Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), $field_name);
    }

    public function addField(Model $model, string $field_name, string $field_type, array $attrs = []): void
    {
        $model = $this->model;
        if (! \Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), $field_name)) {
            \Schema::connection($model->getConnectionName())
                ->table(
                    $model->getTable(),
                    function ($table) use ($field_name, $field_type): void {
                        $table->{$field_type}($field_name);
                    }
                );
        }
    }

    /**
     * execute a query.
     *
     * @return bool
     */
    public function query(string $sql)
    {
        $model = $this->model;
        $res = $model->getConnection()->statement($sql);

        // $res=$model->getConnection()->select($sql);
        return $res;
    }

    /**
     * execute a query.
     *
     * @return array
     */
    public function select(string $sql)
    {
        $model = $this->model;
        // $res=$model->getConnection()->statement($sql);
        $res = $model->getConnection()->select($sql);

        return $res;
    }

    /**
     * Undocumented function.
     *
     * get all tables and fields of the same collection
     */
    public function getAllTablesAndFields(): Collection
    {
        $model = $this->model;
        $connection = $model->getConnection();

        $dbSchemaManager = $connection->getDoctrineSchemaManager();
        $table_names = $dbSchemaManager->listTableNames();

        $data = collect($table_names)->map(
            function ($table_name) use ($dbSchemaManager) {
                $doctrineTable = $dbSchemaManager->listTableDetails($table_name);
                $columns = $doctrineTable->getColumns();

                $fields = collect($columns)->map(
                    function ($col) {
                        return [
                            'name' => $col->getName(),
                            'type' => $col->getType()->getName(),
                        ];
                    });

                return ['name' => $table_name, 'fields' => $fields];
            });

        return $data;
    }

    public function modelExistsByTableName(string $table_name): bool
    {
        $model_ns = \get_class($this->model);
        $model_ns = collect(explode('\\', $model_ns))->slice(0, -1)->implode('\\');

        $model_name = Str::singular($table_name);
        $model_name = Str::studly($model_name);
        if (Str::startsWith($table_name, '_')) {
            $model_name = '_'.$model_name;
        }

        $model_class = $model_ns.'\\'.$model_name;

        return class_exists($model_class);
    }

    /*
    public static function indexIfNotExistsStatic($index, $tbl = null, $conn = null) { //viene chiamato all'interno di filtertrait che e' static ..
        if (null == $tbl) {
            $self = new self();
            $tbl = $self->getTable();
            if (null == $conn) {
                $conn = $self->getConnection();
            }
        }
        if (\is_array($index)) {
            foreach ($index as $i) {
                self::indexIfNotExistsStatic($i, $tbl, $conn);
            }
        } else {
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable = $dbSchemaManager->listTableDetails($tbl);
            //faremo dei controlli per non aggiungere troppe chiavi
            //-- metodo alternativo da testare
            //if (collect(DB::select("SHOW INDEXES FROM persons"))->pluck('Key_name')->contains('persons_body_unique')) {
            //    $table->dropUnique('persons_body_unique');
            //}
            //
            //-- altro metodo da testare
            //    $indexesFound = $dbSchemaManager->listTableIndexes($tbl);
            //
            try {
                if (! $doctrineTable->hasIndex($tbl.'_'.$index.'_index')) {
                    Schema::connection($conn->getName())->table($tbl, function ($table) use ($index) {
                        $table->index($index);
                    });
                }
            } catch (\Exception $e) {
                echo '<small>'.$e->getMessage().'</small>';
            }
        }
    }

    public function indexIfNotExists($index, $tbl = null, $conn = null) {
        if (null == $tbl) {
            $tbl = $this->getTable();
        }
        if (null == $conn) {
            $conn = $this->getConnection();
        }
        if (\is_array($index)) {
            foreach ($index as $i) {
                $this->indexIfNotExists($i, $tbl, $conn);
            }
        } else {
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable = $dbSchemaManager->listTableDetails($tbl);
            //faremo dei controlli per non aggiungere troppe chiavi
            if (! $doctrineTable->hasIndex($tbl.'_'.$index.'_index')) {
                Schema::connection($conn->getName())->table($tbl, function ($table) use ($index) {
                    $table->index($index);
                });
            }
        }
    }
    */
}

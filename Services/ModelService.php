<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

//----------- Requests ----------
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Modules\Xot\Contracts\ModelContract;
use ReflectionClass;
use ReflectionMethod;

// per dizionario morph
//------------ services ----------

/**
 * Class ModelService.
 */
class ModelService {
    /**
     * Undocumented function.
     *
     * @param ModelContract|Model $model
     */
    public static function getRelationshipsAndData($model, array $data): array {
        $methods = get_class_methods($model);
        //Access to an undefined property Illuminate\Database\Eloquent\Model|Modules\Xot\Contracts\ModelContract::$post_type.
        Relation::morphMap([$model->post_type => get_class($model)]);
        $data = collect($data)->filter(
            function ($item, $key) use ($methods) {
                return in_array($key, $methods);
            }
            )->map(
                function ($v, $k) use ($model, $data) {
                    if (! is_string($k)) {
                        dddx([$k, $v, $data]);
                    }
                    $rows = $model->$k();
                    $related = null;
                    if (method_exists($rows, 'getRelated')) {
                        $related = $rows->getRelated();
                    }

                    return (object) [
                        'relationship_type' => class_basename($rows),
                        'is_relation' => $rows instanceof \Illuminate\Database\Eloquent\Relations\Relation,
                        'related' => $related,
                        'data' => $v,
                        'name' => $k,
                        'rows' => $rows,
                    ];
                }
            )->all();

        return $data;
    }

    /**
     * Undocumented function
     * funziona leggendo o il "commento" prima della funzione o quello che si dichiara come returnType.
     *
     * @param [type] $model
     *
     * @return void
     */
    public static function getRelations($model) {
        $reflector = new ReflectionClass($model);
        $relations = [];
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $returnType = $method->getReturnType();
            $doc = $method->getDocComment();
            $res = $method->getName();

            if (0 == $method->getNumberOfRequiredParameters() && $method->class == get_class($model)) {
                if ($returnType && false !== strpos($returnType->getName(), '\\Relations\\')) {
                    //if (in_array(class_basename($returnType->getName()), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
                    $relations[] = $res;
                } elseif ($doc && false !== strpos($doc, '\\Relations\\')) {
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
     * @param [type] $model
     *
     * @return void
     *              https://laracasts.com/discuss/channels/eloquent/get-all-model-relationships
     */
    public static function getRelationships($model) {
        $relationships = [];

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($model) ||
                ! empty($method->getParameters()) ||
                __FUNCTION__ == $method->getName()) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof Relation) {
                    $relationships[$method->getName()] = [
                        'name' => $method->getName(),
                        'type' => (new ReflectionClass($return))->getShortName(),
                        'model' => (new ReflectionClass($return->getRelated()))->getName(),
                    ];
                }
            } catch (ErrorException $e) {
            }
        }

        return $relationships;
    }

    public static function getNameRelationships($model): array {
        $relations = self::getRelationships($model);
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
    public static function indexIfNotExists(Model $model, $index): void {
        if (\is_array($index)) {
            foreach ($index as $i) {
                self::indexIfNotExists($model, $i);
            }
        } else {
            $tbl = $model->getTable();
            $conn = $model->getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable = $dbSchemaManager->listTableDetails($tbl);
            //faremo dei controlli per non aggiungere troppe chiavi
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

    public static function fieldExists(Model $model, string $field_name): bool {
        return \Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), $field_name);
    }

    public static function addField(Model $model, string $field_name, string $field_type, array $attrs = []): void {
        if (! \Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), $field_name)) {
            \Schema::connection($model->getConnectionName())
                ->table($model->getTable(),
                    function ($table) use ($field_name, $field_type): void {
                        $table->{$field_type}($field_name);
                    }
                );
        }
    }

    /**
     * execute a query.
     *
     * @param [type] $model
     */
    public static function query($model, string $sql): void {
        $model->getConnection()->statement($sql);
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
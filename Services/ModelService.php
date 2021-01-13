<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

//----------- Requests ----------
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Modules\Xot\Contracts\ModelContract;

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
        Relation::morphMap([$model->post_type => get_class($model)]);
        $data = collect($data)->filter(
            function ($item, $key) use ($methods) {
                return in_array($key, $methods);
            }
            )->map(
                function ($v, $k) use ($model,$data) {
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

    public static function indexIfNotExists(ModelContract $model, $index): void {
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
                    function ($table) use ($index) {
                        $table->index($index);
                    }
                );
            }
        }
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
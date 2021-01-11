<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

//----------- Requests ----------
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\Xot\Contracts\ModelContract;

// per dizionario morph
//------------ services ----------

/**
 * Class ModelService.
 */
class ModelService {
    public static function getRelationshipsAndData(ModelContract $model, array $data): array {
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
}

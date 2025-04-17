<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Store;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Modules\Xot\DTOs\RelationDTO;
use Spatie\QueueableAction\QueueableAction;

class MorphToManyAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, RelationDTO $relation): void
    {
        if (! $relation->rows instanceof MorphToMany) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        $data = $relation->data;
        if (\in_array('to', array_keys($data), true) || \in_array('from', array_keys($data), true)) {
            if (! isset($data['to'])) {
                $data['to'] = [];
            }
            $data = $data['to'];
        }
        // dddx(['row' => $row, 'relation' => $relation, 't1' => Arr::isAssoc($data)]);

        if (! Arr::isAssoc($data)) {
            $relation->rows->sync($data);

            return;
        }

        dddx([
            'message' => 'wip',
            'row' => $row,
            'relation' => $relation,
            'relation_rows' => $relation->rows->exists(),
            't' => $row->{$relation->name},
        ]);

        dddx('wip');
        /*
        foreach ($data as $k => $v) {
            if (\is_array($v)) {
                if (! isset($v['pivot'])) {
                    $v['pivot'] = [];
                }
                if (! isset($v['pivot']['user_id']) && isset($model->user_id)) {
                    $v['pivot']['user_id'] = $model->user_id;
                }
                if (! isset($v['pivot']['user_id']) && \Auth::check()) {
                    $v['pivot']['user_id'] = \Auth::id();
                }
                $model->$name()->syncWithoutDetaching([$k => $v['pivot']]);
            } else {
                $res = $model->$name()->syncWithoutDetaching([$v]);
            }
        }
        */
    }
}

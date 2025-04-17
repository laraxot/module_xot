<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Store;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Xot\DTOs\RelationDTO;
use Spatie\QueueableAction\QueueableAction;

class BelongsToAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, RelationDTO $relation): void
    {
        if (! $relation->rows instanceof BelongsTo) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        $related = $relation->rows->create($relation->data);

        $relation->rows->associate($related);

        // $rows = $relation->rows;

        // dd([$relation->name]);
        /*
        if (null == $row->{$relation->name}) {
            $row->{$relation->name}()->create($relation->data);

            return;
        }
        */
        /*
        dddx([
            'message' => 'wip',
            'row' => $row,
            'relation' => $relation,
            'relation_rows' => $relation->rows->exists(),
            't' => $row->{$relation->name},
            't1'=>$rows,
        ]);
        */
    }
}

<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
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
        $rows = $relation->rows;

        if (! \is_array($relation->data)) {
            $related = $rows->getRelated();
            $related = $related->find($relation->data);
            $res = $rows->associate($related);
            $res->save();

            return;
        }

        if (! Arr::isAssoc($relation->data) && 1 == count($relation->data)) {
            $related_id = $relation->data[0];
            $related = $relation->related->find($related_id);
            $res = $rows->associate($related);
            $res->save();

            return;
        }

        if (Arr::isAssoc($relation->data)) {
            $sub = $rows->first();
            if (null == $sub) {
                throw new \Exception('['.__LINE__.']['.__FILE__.']');
            }
            app(RelationAction::class)->execute($sub, $relation->data);
        }

        $fillable = collect($relation->related->getFillable())->merge($relation->related->getHidden());
        $data = collect($relation->data)->only($fillable)->all();

        if ($rows->exists()) {
            // $rows->update($data); // non passa per il mutator
            $row->{$relation->name}->update($data);

            return;
        }

        // dddx([$relation->related, $data]);

        $related = $relation->related->create($data);
        $res = $rows->associate($related);
        $res->save();
    }
}

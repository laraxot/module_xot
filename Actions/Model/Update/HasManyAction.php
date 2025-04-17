<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueueableAction\QueueableAction;

class HasManyAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    public function execute(Model $row, \Modules\Xot\DTOs\RelationDTO $relation)
    {
        if (! $relation->rows instanceof HasMany) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        if (isset($relation->data['from']) && isset($relation->data['to'])) {
            $f_key = $relation->rows->getForeignKeyName();
            $res = $relation->related->where($f_key, $row->getKey())
                ->update([$f_key => null]);
            foreach ($relation->data['to'] as $item) {
                $row0 = $relation->related
                    ->where('id', $item)
                    ->update([$f_key => $row->getKey()]);
            }

            return;
        }

        $rows = $relation->rows;
        $rows->update($relation->data);
    }
}

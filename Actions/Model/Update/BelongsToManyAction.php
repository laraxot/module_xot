<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class BelongsToManyAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, \Modules\Xot\DTOs\RelationDTO $relation): void
    {
        // dddx(['row' => $row, 'relation' => $relation]);
        if (\in_array('to', array_keys($relation->data), true) || \in_array('from', array_keys($relation->data), true)) {
            // $this->saveMultiselectTwoSides($row, $relation->name, $relation->data);
            $to = $relation->data['to'] ?? [];

            $row->{$relation->name}()->sync($to);
            $status = 'collegati ['.implode(', ', $to).'] ';
            \Session::flash('status', $status);

            return;
        }
        $row->{$relation->name}()->sync($relation->data);
    }
}

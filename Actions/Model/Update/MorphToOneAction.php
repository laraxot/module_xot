<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Fidum\EloquentMorphToOne\MorphToOne;
use Illuminate\Database\Eloquent\Model;
use Modules\Xot\DTOs\RelationDTO;
use Spatie\QueueableAction\QueueableAction;

class MorphToOneAction
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
    public function execute(Model $row, RelationDTO $relation)
    {
        // dddx(['row' => $row, 'relation' => $relation]);
        if (! $relation->rows instanceof MorphToOne) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        $rows = $relation->rows;

        // if (is_array($relation->data)) {
        if (! isset($relation->data['lang'])) {
            $relation->data['lang'] = \App::getLocale();
        }
        $rows->create($relation->data);
        // }
        // else {
        //    $rows->sync($relation->data);
        // }
    }
}

<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Xot\DTOs\RelationDTO;
use Spatie\QueueableAction\QueueableAction;

class HasOneAction
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
        if (! $relation->rows instanceof HasOne) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        $rows = $relation->rows;
        // $rows= $row->{$relation->name}();
        if ($rows->exists()) {
            if (! \is_array($relation->data)) {
                // variabile uguale alla relazione
            } else {
                // backtrace(true);
                // dddx([$model, $name, $data]);
                $row->{$relation->name}->update($relation->data);
            }
        } else {
            dddx(['err' => 'wip']);
            // $this->storeRelationshipsHasOne($params);
        }
    }
}

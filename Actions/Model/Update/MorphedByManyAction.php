<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class MorphedByManyAction
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
        dddx('wip');
        /*
        foreach ($data as $k => $v) {
            if (! \is_array($v)) {
                $v = [];
            }
            if (! isset($v['pivot'])) {
                $v['pivot'] = [];
            }
            // Call to undefined method Illuminate\Database\Eloquent\Relations\MorphMany::syncWithoutDetaching()
            // $res = $model->$name()->syncWithoutDetaching([$k => $v['pivot']]);
            $model->$name()->touch();
        }
        */
    }
}

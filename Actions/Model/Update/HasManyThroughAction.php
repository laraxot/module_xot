<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class HasManyThroughAction
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
    }
}

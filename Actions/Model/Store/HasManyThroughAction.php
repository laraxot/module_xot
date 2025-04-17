<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Store;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class HasManyThroughAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, \Modules\Xot\DTOs\RelationDTO $relation): void
    {
        dddx('wip');
    }
}

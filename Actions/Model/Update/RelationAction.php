<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Modules\Xot\Actions\Model\FilterRelationsAction;
use Spatie\QueueableAction\QueueableAction;

class RelationAction
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
    public function execute(Model $row, array $data)
    {
        $relations = app(FilterRelationsAction::class)->execute($row, $data);
        /*
        if ('CompanyService' != class_basename($row)) {
            dddx([
                'basename' => class_basename($row),
                'row' => $row,
                'data' => $data,
                'relations' => $relations,
            ]);
        }
        */
        foreach ($relations as $relation) {
            $act = __NAMESPACE__.'\\'.$relation->relationship_type.'Action';
            app($act)->execute($row, $relation);
        }
    }
}

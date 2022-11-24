<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class MorphToOneAction {
    use QueueableAction;

    public function __construct() {
    }

    public function execute(Model $row, object $relation) {
        // dddx(['row' => $row, 'relation' => $relation]);
        $rows = $relation->rows;
        if ($rows->exists()) {
            $rows->update($relation->data);
        } else {
            if (is_array($relation->data)) {
                if (! isset($relation->data['lang'])) {
                    $relation->data['lang'] = \App::getLocale();
                }
                $rows->create($relation->data);
            } else {
                $rows->sync($relation->data);
            }
        }
    }
}
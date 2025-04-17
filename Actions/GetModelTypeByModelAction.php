<?php
/**
 * @see https://github.com/protonemedia/laravel-ffmpeg
 */
declare(strict_types=1);

namespace Modules\Xot\Actions;

use Illuminate\Support\Str;
use Modules\Xot\Contracts\ModelContract;
use Spatie\QueueableAction\QueueableAction;

class GetModelTypeByModelAction
{
    use QueueableAction;

    /**
     * Execute the action.
     */
    public function execute(ModelContract $model): string
    {
        $model_class = Str::snake(class_basename($model));

        return $model_class;
    }
}

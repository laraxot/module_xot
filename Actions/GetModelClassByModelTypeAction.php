<?php
/**
 * @see https://github.com/protonemedia/laravel-ffmpeg
 */
declare(strict_types=1);

namespace Modules\Xot\Actions;

use Spatie\QueueableAction\QueueableAction;

class GetModelClassByModelTypeAction
{
    use QueueableAction;

    /**
     * Execute the action.
     */
    public function execute(string $model_type): string
    {
        $morph_map = config('morph_map');
        if (! is_array($morph_map)) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        $model_class = collect($morph_map)->get($model_type);

        return $model_class;
    }
}

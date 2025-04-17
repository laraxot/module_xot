<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class DetachAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, array $data, array $rules): Model
    {
        if (! isset($row->pivot)) {
            return $row;
        }
        $res = $row->pivot->delete();
        if ($res) {
            \Session::flash('status', 'scollegato');
        } else {
            \Session::flash('status', 'NON scollegato');
        }

        return $row;
    }
}

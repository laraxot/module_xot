<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\QueueableAction\QueueableAction;

class UpdateAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, array $data, array $rules): Model
    {
        $validator = Validator::make($data, $rules);
        $validator->validate();

        // dddx($data);

        try {
            $row = tap($row)->update($data);
        } catch (\Exception $e) {
            if ('Node must exists.' === $e->getMessage()) {
                app($row::class)->fixTree();
                $row = tap($row)->update($data);
            }
        }

        app(__NAMESPACE__.'\\Update\RelationAction')->execute($row, $data);

        $msg = 'aggiornato! ['.$row->getKey().']!'; // .'['.implode(',',$row->getChanges()).']';

        Session::flash('status', $msg); // .

        return $row;
    }
}

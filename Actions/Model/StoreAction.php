<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\QueueableAction\QueueableAction;

class StoreAction
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(Model $row, array $data, array $rules): Model
    {
        if (! isset($data['lang']) && \in_array('lang', $row->getFillable(), true)) {
            $data['lang'] = app()->getLocale();
        }
        /*if (
            ! isset($data['user_id'])
            && \in_array('user_id', $row->getFillable(), true)
            && 'user_id' !== $row->getKeyName()
        ) {
            $data['user_id'] = \Auth::id();
        }*/

        $validator = Validator::make($data, $rules);
        $validator->validate();

        $row = $row->fill($data);

        $row->save();

        $relations = app(FilterRelationsAction::class)->execute($row, $data);

        foreach ($relations as $relation) {
            $act = __NAMESPACE__.'\\Store\\'.$relation->relationship_type.'Action';
            // dddx(['act'=>$act,'row'=>$row,'relation'=>$relation,'data'=>$data]);
            // if (\is_array($data[$relation->name])) {
            // $relation->data = $data[$relation->name];
            app($act)->execute($row, $relation);
            // }
        }

        $msg = 'created! ['.$row->getKey().']!';

        Session::flash('status', $msg); // .

        return $row;
    }
}

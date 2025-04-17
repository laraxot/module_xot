<?php

declare(strict_types=1);

namespace Modules\Xot\Actions\Model\Update;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Xot\DTOs\RelationDTO;
use Spatie\QueueableAction\QueueableAction;

class MorphToManyAction
{
    use QueueableAction;

    public Collection $res;

    public function __construct()
    {
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    public function execute(Model $row, RelationDTO $relation)
    {
        $data = $relation->data;
        $name = $relation->name;
        $model = $row;
        if (! is_array($data)) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }
        if (\in_array('to', array_keys($data), true) || \in_array('from', array_keys($data), true)) {
            if (! isset($data['to'])) {
                $data['to'] = [];
            }

            $data = $data['to'];
        }

        if (! \Arr::isAssoc($data)) {
            // dddx(['model' => $model, 'name' => $name, 'data' => $data]);
            $model->{$name}()->sync($data);
        }

        foreach ($data as $k => $v) {
            if (\is_array($v)) {
                if (! isset($v['pivot'])) {
                    $v['pivot'] = [];
                }
                // dddx('a');
                /*
                echo '<hr/><pre>'.print_r($v['pivot'],1).'</pre><hr/>';
                $res = $model->$name()
                        ->where('related_id',$k)
                        ->where('user_id',$v['pivot']['user_id'])
                        ->update($v['pivot']);
                */

                $res = $model->$name()
                    ->syncWithoutDetaching([$k => $v['pivot']]);
            } else {
                // $res = $model->$name()
                //   ->syncWithoutDetaching([$v]);
            }
            // ->where('user_id',1)
            // ->syncWithoutDetaching([$k => $v['pivot']])

            // ->updateOrCreate(['related_id'=>$k,'user_id'=>1],$v['pivot']);
            // $model->$name()->touch();
        }
    }
}

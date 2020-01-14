<?php

namespace Modules\Xot\Jobs\Crud\Traits;

//----------- Requests ----------
use Modules\Xot\Http\Requests\XotRequest;
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

trait CommonTrait {
    public function getData() {
        $panel = Panel::get($this->row);
        $request = XotRequest::capture();
        $request->validatePanel($panel);
        $data = $request->all();

        return $data;
    }

    /**
     * https://hackernoon.com/eloquent-relationships-cheat-sheet-5155498c209
     * https://laracasts.com/discuss/channels/eloquent/cleanest-way-to-save-model-and-relationships.
     */
    public function manageRelationships($params) {
        extract($params);
        $methods = get_class_methods($model);
        /*
        if(!is_array($methods)){
            $methods=[];
        }
        */
        $data1 = collect($data)->filter(function ($item, $key) use ($methods) {
            return in_array($key, $methods);
        })->map(function ($v, $k) use ($model) {
            return (object) [
                'relationship_type' => class_basename($model->$k()),
                'data' => $v,
                'name' => $k,
            ];
        })->all();

        foreach ($data1 as $k => $v) {
            $func = $act.'Relationships'.$v->relationship_type; //updateRelationshipsMorphOne
            //$this->$func(['model'=>$model,'name'=>$v->name,'data'=>$v->data]);
            $parz = array_merge($params, ['model' => $model, 'name' => $v->name, 'data' => $v->data]);
            self::$func($parz);
        }

        if (isset($data['pivot'])) {
            $func = $act.'Relationships'.'Pivot'; //updateRelationshipsMorphOne
            //$this->$func(['model'=>$model,'name'=>'pivot','data'=>$data['pivot']]);
            //self::$func(['model'=>$model,'name'=>'pivot','data'=>$data['pivot']]);
            $params['name'] = 'pivot';
            $params['data'] = $data['pivot'];
            self::$func($params);
        }
    }
}

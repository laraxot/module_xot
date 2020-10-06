<?php

namespace Modules\Xot\Jobs\Crud\Traits;

use Illuminate\Database\Eloquent\Relations\Relation; // per dizionario morph
//----------- Requests ----------
use Modules\Xot\Http\Requests\XotRequest;
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

trait CommonTrait {
    public function getData() {
        $panel = Panel::get($this->row);
        if (! is_object($panel)) {
            //dddx($this->row);
        }
        $request = XotRequest::capture();
        if (count($request->all()) > 0) {
            //dd(['['.__LINE__.']['.__FILE__.']', $request->all(), request()->all(), $_SESSION]);
            $request->validatePanel($panel);
            $data = $request->all();
        } else {
            $data = request()->all();
        }

        return $data;
    }

    /**
     * https://hackernoon.com/eloquent-relationships-cheat-sheet-5155498c209
     * https://laracasts.com/discuss/channels/eloquent/cleanest-way-to-save-model-and-relationships.
     */
    public function manageRelationships($params) {
        extract($params);
        if (! is_object($model)) {
            return;
            dddx(['model' => $model]);
        }
        $methods = get_class_methods($model);
        //dddx($model->post_type);
        Relation::morphMap([$model->post_type => get_class($model)]);
        /*
        if(!is_array($methods)){
            $methods=[];
        }
        */
        $data1 = collect($data)->filter(function ($item, $key) use ($methods) {
            return in_array($key, $methods);
        })->map(function ($v, $k) use ($model) {
            $rows = $model->$k();
            $related = null;
            if (method_exists($rows, 'getRelated')) {
                $related = $rows->getRelated();
            }

            return (object) [
                'relationship_type' => class_basename($rows),
                'is_relation' => $rows instanceof \Illuminate\Database\Eloquent\Relations\Relation,
                'related' => $related,
                'data' => $v,
                'name' => $k,
                'rows' => $rows,
            ];
        })->all();

        foreach ($data1 as $k => $v) {
            //Relation::morphMap();
            //if($v->related->post_type!=null){
                //dddx($v);
            //}
            $func = $act.'Relationships'.$v->relationship_type; //updateRelationshipsMorphOne
            //echo '<h3>'.$func.'</h3>';
            //$this->$func(['model'=>$model,'name'=>$v->name,'data'=>$v->data]);
            $parz = array_merge($params, ['model' => $model, 'name' => $v->name, 'data' => $v->data]);
            /*
            if(!method_exists($this,$func)){
                dddx(
                    [
                        'func'=>$func,
                        'model'=>$model,
                        'model_class'=>get_class($model),
                        'name'=>$v->name,
                        'data'=>$v->data,
                        'v'=>$v,
                    ]
                );
            }
            */
            //if($v->is_relation){
            if (method_exists($this, $func)) {
                self::$func($parz);
            }
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

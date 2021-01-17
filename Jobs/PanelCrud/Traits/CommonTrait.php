<?php

namespace Modules\Xot\Jobs\PanelCrud\Traits;

use Carbon\Carbon;
//----------- Requests ----------
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // per dizionario morph
//------------ services ----------
use Modules\Xot\Http\Requests\XotRequest;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Trait CommonTrait
 * @package Modules\Xot\Jobs\PanelCrud\Traits
 */
trait CommonTrait {
    /**
     * @return array
     */
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
     * @param array $params
     */
    public function manageRelationships($params) {
        $act = 'show';
        extract($params);
        if (! isset($model)) {
            dddx(['err' => 'model is missing']);

            return;
        }
        if (! isset($data)) {
            dddx(['err' => 'data is missing']);

            return;
        }
        if (! is_object($model)) {
            return;
            //dddx(['model' => $model]);
        }
        $methods = get_class_methods($model);
        //dddx($model->post_type);
        Relation::morphMap([$model->post_type => get_class($model)]);
        /*
        if(!is_array($methods)){
            $methods=[];
        }
        */
        //dddx($params);
        $data1 = collect($data)->filter(function ($item, $key) use ($methods) {
            return in_array($key, $methods);
        })->map(function ($v, $k) use ($model,$data) {
            if (! is_string($k)) {
                dddx([$k, $v, $data]);
            }
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

    /**
     * @param array $data
     * @param PanelContract $panel
     * @return mixed
     */
    public function prepareForValidation($data, $panel) {
        $date_fields = collect($panel->fields())->filter(
            function ($item) use ($data) {
                return Str::startsWith($item->type, 'Date') && isset($data[$item->name]);
            }
        )->all();
        foreach ($date_fields as $field) {
            $value = $data[$field->name]; // metterlo nel filtro sopra ?
            /*
            *  Se e' un oggetto e' già convertito
            **/
            if (! is_object($value)) {
                $func = 'Conv'.$field->type;
                $value_new = $this->$func($field, $value);
                //$this->request->add([$field->name => $value_new]);
                $data[$field->name] = $value_new;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param PanelContract $panel
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function prepareAndValidate($data, $panel) {
        $data = $this->prepareForValidation($data, $panel);
        $act = '';
        $rules = $panel->rules(['act' => $act]);

        $validator = Validator::make($data, $rules);

        return $validator->validate(); //fa tutto da solo
    }

    /**
     * @param $field
     * @param mixed $value
     * @return Carbon|false|null
     */
    public function ConvDate($field, $value) {
        if (null == $value) {
            return $value;
        }
        $value_new = Carbon::createFromFormat('d/m/Y', $value);

        return $value_new;
    }

    /**
     * @param $field
     * @param mixed $value
     * @return Carbon|false|null
     */
    public function ConvDateTime($field, $value) {
        if (null == $value) {
            return $value;
        }
        $value_new = Carbon::createFromFormat('d/m/Y H:i', $value);

        return $value_new;
    }

    /**
     * @param $field
     * @param mixed $value
     * @return Carbon|false|null
     */
    public function ConvDateTime2Fields($field, $value) {
        if (null == $value) {
            return $value;
        }
        $value_new = Carbon::createFromFormat('d/m/Y H:i', $value);

        return $value_new;
    }
}

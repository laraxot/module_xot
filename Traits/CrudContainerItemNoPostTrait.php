<?php

namespace Modules\Xot\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
//-------- services -----
//-------- models ------
//use Modules\Blog\Models\Post; // deve essere slegato dal modulo Blog
//------- services -------
//use Modules\Theme\Services\ThemeService;  // qui restituisco il Panel che poi mostrera' i dati
//use Modules\Xot\Services\PolicyService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\StubService;

trait CrudContainerItemNoPostTrait {
    public function index(Request $request, $container, $item) {
        if (false === $item) {
            $row = self::getXotModel($container);
            $rows = $row;
        } else {
            $types = Str::camel(Str::plural($container));
            $rows = $item->$types();
            $row = $rows->getRelated();
        }
        //$rows = $rows->paginate(20);
        $panel = Panel::get($row);
        $panel->setRows($rows);

        return $panel;
    }

    public function create(Request $request, $container, $item) {
        $types = Str::camel(Str::plural($container));
        if (is_object($item)) { //l'oggetto figlio potrebbe avere un modello diverso
            $rows = $item->$types();
            $row = $rows->getRelated();
        } else {
            $rows = null;
            $row = xotModel($container);
        }
        //$rows = $rows->paginate(20);
        $panel = Panel::get($row);
        $panel->setRows($rows);

        return $panel;
    }

    public function store(Request $request, $container, $item) {
        $data = $request->all();
        $types = Str::camel(Str::plural($container));
        if (is_object($item)) { //l'oggetto figlio potrebbe avere un modello diverso
            $rows = $item->$types();
            $related = $rows->getRelated();
            //$related_class=get_class($related);
            //$row=new $related_class;
            $row = $related;
        } else {
            $rows = null;
            $row = xotModel($container);
        }

        if (! isset($data['lang']) && in_array('lang', $row->getFillable())) {
            $data['lang'] = \App::getLocale();
        }

        $item_new = $row->fill($data);
        $item_new->save();

        if (false !== $item) {
            if (! is_object($item)) {
                ddd($item);
            }
            $pivot_data = [];
            if (isset($data['pivot'])) {
                $pivot_data = $data['pivot'];
            }
            $tmp = $item->$types()->save($item_new, $pivot_data);
        }
        $panel = StubService::getByModel($item_new, 'panel');
        self::manageRelationships(['model' => $item_new, 'data' => $data, 'act' => 'store', 'container' => $container, 'item' => $item, 'rows' => $rows]);
        if (method_exists($panel, 'storeCallback')) {
            $item_new = $panel->storeCallback(['row' => $item_new, 'data' => $data]);
        }
        \Session::flash('status', 'aggiornato! ['.$row->getKey().']!'); //.implode(',',$row->getChanges())
        $panel = Panel::get($item_new);

        return $panel;
    }

    public function edit(Request $request, $container, $item) {
        return Panel::get($item);
    }

    public function update(Request $request, $container, $item) {
        $data = $request->all();
        $row = $item;
        $panel = Panel::get($row);
        $ris = $row->update($data);
        $this->manageRelationships(['model' => $item, 'data' => $data, 'act' => 'update']);
        \Session::flash('status', 'aggiornato! ['.$row->getKey().']!'); //.implode(',',$row->getChanges())
        return Panel::get($item);
    }

    public function show(Request $request, $container, $item) {
        return Panel::get($item);
    }

    /**
     * https://hackernoon.com/eloquent-relationships-cheat-sheet-5155498c209
     * https://laracasts.com/discuss/channels/eloquent/cleanest-way-to-save-model-and-relationships.
     */
    public function manageRelationships($params) {
        //ddd($params);
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

    public function updateRelationshipsHasOne($params) {
        extract($params);
        $rows = $model->$name();
        if ($rows->exists()) {
            if (! is_array($data)) {
                //variabile uguale alla relazione
            } else {
                $model->$name()->update($data);
            }
        } else {
            $this->storeRelationshipsHasOne($params);
        }
    }

    public function updateRelationshipsBelongsToMany($params) {
        extract($params);
        //$rows = $model->$name();
        if (isset($data['from']) || isset($data['to'])) {
            $this->saveMultiselectTwoSides($params);
        //ddd($params);
        } else {
            //ddd('TO DO');
        }
    }

    public function updateRelationshipsBelongsTo($params) {
        extract($params);
        $rows = $model->$name();
        if ($rows->exists()) {
            $model->$name()->update($data);
        //$model->$name->update($data);
        } else {
            $this->storeRelationshipsBelongsTo($params);
        }
    }

    public function updateRelationshipsMorphOne($params) {
        extract($params);
        $model->$name()->update($data);
    }

    public function updateRelationshipsMorphToMany($params) {
        extract($params);
        //$res=$model->$name()->syncWithoutDetaching($data);
        foreach ($data as $k => $v) {
            if (! is_array($v)) {
                $v = [];
            }
            if (! isset($v['pivot'])) {
                $v['pivot'] = [];
            }
            $res = $model->$name()->syncWithoutDetaching([$k => $v['pivot']]);
            $model->$name()->touch();
        }
    }

    public function updateRelationshipsPivot($params) {
        extract($params);
        $model->$name->update($data);
    }

    public function storeRelationshipsPivot($params) {
        /*
        extract($params);
        $types=Str::plural($container);
        //ddd($params);
        //$model->$name()->create($data);
        $k=$model->getKey();
        $res=$item->$types()->update($model,$data);
        //ddd($res);
        */
    }

    public function storeRelationshipsHasOne($params) {
        extract($params);
        $rows = $model->$name();
        //debug_getter_obj(['obj'=>$rows]);
        $related = $rows->create($data);
        if (! $model->$name()->exists()) {//collegamento non riuscito
            $pk_local = $rows->getLocalKeyName();
            $pk_fore = $rows->getForeignKeyName();
            $data1 = [$pk_local => $related->$pk_fore];
            $model->update($data1);
        }
    }

    public function storeRelationshipsHasMany($params) {
        extract($params);
        //$rows = $model->$name();
    }

    public function storeRelationshipsBelongsTo($params) {
        extract($params);
        $rows = $model->$name();
        //debug_getter_obj(['obj'=>$rows]);
        $related = $rows->create($data);
        //$model->$name()->save($related); //Call to undefined method Illuminate\Database\Eloquent\Relations\BelongsTo::save()
        if (! $model->$name()->exists()) {//collegamento non riuscito
            $pk_own = $rows->getOwnerKeyName();
            $pk_fore = $rows->getForeignKeyName();
            $data1 = [$pk_fore => $related->$pk_own];
            $model->update($data1);
        }
    }

    /* -- non so se la utilizzo, dopo cancello
    public function formatData($params) {
        extract($params);
        $panel = StubService::getByModel(new $class(), 'panel');
        //ddd($panel->fields());
        //ddd($data);
        $fields = collect($panel->fields())->filter(function ($item) use ($data) {
            return in_array($item->name, array_keys($data));
        })->all();
        //ddd($fields);
        return $data;
    }
    */

    public function storeRelationshipsMorphOne($params) {
        extract($params);
        if (! isset($data['lang']) /* && in_array('lang', $row->getFillable()) */) {
            $data['lang'] = \App::getLocale();
        }
        if ($model->$name()->exists()) {
            $model->$name()->update($data);
        } else {
            $model->$name()->create($data);
        }
    }

    public function storeRelationshipsMorphToMany($params) {
        extract($params);

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                if (! isset($v['pivot'])) {
                    $v['pivot'] = [];
                }
                if (! isset($v['pivot']['auth_user_id']) && isset($model->auth_user_id)) {
                    $v['pivot']['auth_user_id'] = $model->auth_user_id;
                }
                if (! isset($v['pivot']['auth_user_id']) && \Auth::check()) {
                    $v['pivot']['auth_user_id'] = \Auth::user()->auth_user_id;
                }
                /*
                * syncWithoutDetaching fa una select a vuoto ma funziona
                *
                **/
                $model->$name()->syncWithoutDetaching([$k => $v['pivot']]);
            } else {
                /*
                $rows1=$model->$name();
                $related=$rows1->getRelated();
                ddd($related);
                //ddd($params);
                */
                //$model->$name()->attach()
            }
        }
    }

    public function indexAttach(Request $request, $container, $item) {
        if ('POST' == $request->getMethod()) {
            $this->indexAttachSave($request, $container, $item);
        }
        $types = Str::camel(Str::plural($container));
        if (is_object($item)) {
            $rows = $item->$types();
            $pivot_class = $rows->getPivotClass();
            $pivot = new $pivot_class();
            $panel = StubService::getByModel($pivot, 'panel', true);
            $panel->pivot_key_names = [];
            $panel->pivot_key_names[] = $rows->getForeignPivotKeyName();
            if (method_exists($rows, 'getMorphType')) {
                $panel->pivot_key_names[] = $rows->getMorphType();
            }

            return $panel;
        }
        //return $this->create($request,$container,$item); //crea collegamento ..
    }

    public function indexAttachSave(Request $request, $container, $item) {
        $data = $request->all();
        $types = Str::camel(Str::plural($container));
        $related_pivot_key_name = $item->$types()->getRelatedPivotKeyName();
        $related_pivot_key_value = $data[$related_pivot_key_name];
        $rows = $item->$types()->attach($related_pivot_key_value, $data); //forse dovremmo aggiungere il tipo di relazione
        //ddd($data);
        /*
        if(is_object($item)){
            $rows=$item->$types();
            $pivot_class=$rows->getPivotClass();
            $foreign_pivot_key_name=$rows->getForeignPivotKeyName();
            $pivot=new $pivot_class;
            $pivot->
        };
        ddd('senza item ?');
        */
        //return ThemeService::action($request, $item);
    }

    public function indexEdit(Request $request, $container, $item) {
        //ddd($request->getMethod());
        if ('POST' == $request->getMethod()) {
            //return  //
            $this->indexUpdate($request, $container, $item);
            \Session::flash('status', 'Successo ! ');
            /*
            if ($request->ajax()) {
                return json_encode(['msg' => 'ok']);
            }
            */
        }

        return $this->index($request, $container, $item);
        //return self::index($request,$container,$item);
    }

    public function indexUpdate(Request $request, $container, $item) {
        $data = $request->all();
        /* --- da cambiare js --
        $types = Str::camel(Str::plural($container));
        if (isset($data[$types]['from']) || isset($data[$types]['to'])) {
            $this->saveMultiselectTwoSides($request, $container, $item);
        }
        */
        $this->manageRelationships(['model' => $item, 'data' => $data, 'act' => 'indexUpdate']);
    }

    public function indexUpdateRelationshipsMorphToMany($params) {
        extract($params);
        //ddd($data);
        /*
        $auth_user_id=\Auth::user()->auth_user_id;
        $data=collect($data)->map(function($item) use($auth_user_id){
            $item['auth_user_id']=$auth_user_id;
            return $item;
        })->all();
        $res=$model->$name()->syncWithoutDetaching($data);
        //*/
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                if (! isset($v['pivot'])) {
                    $v['pivot'] = [];
                }
                if ((! isset($v['pivot']['auth_user_id']) || '' == $v['pivot']['auth_user_id']) && \Auth::check()) {
                    $v['pivot']['auth_user_id'] = \Auth::user()->auth_user_id;
                }
                $model->$name()->syncWithoutDetaching([$k => $v['pivot']]);
            } else {
                ddd('to do-- ovvero da fare');
                /*
                $rows1=$model->$name();
                $related=$rows1->getRelated();
                ddd($related);
                //ddd($params);
                */
                //$model->$name()->attach()
            }
        }
    }

    public function indexUpdateRelationshipsBelongsToMany($params) {
        extract($params);
        if (isset($data['from']) || isset($data['to'])) {
            $this->saveMultiselectTwoSides($params);
            $data['from'] = [];
            unset($data['from']);
            $data['to'] = [];
            unset($data['to']);
        }
    }

    public function saveMultiselectTwoSides($params) { //passo request o direttamente data ?
        extract($params);
        $items = $model->$name();
        $related = $items->getRelated();
        //ddd($related);
        $container_obj = $model;
        //$items_key = $container_obj->getKeyName();
        $items_key = $related->getKeyName();
        //ddd($items_key);//auth_user_id
        $items_0 = $items->get()->pluck($items_key);

        if (! isset($data['to'])) {
            $data['to'] = [];
        }
        $items_1 = collect($data['to']);
        $items_add = $items_1->diff($items_0);
        $items_sub = $items_0->diff($items_1);
        $items->detach($items_sub->all());
        /* da risolvere Column not found: 1054 Unknown column 'related_type' liveuser_area_admin_areas */
        try {
            $items->attach($items_add->all(), ['related_type' => $container]);
        } catch (\Exception $e) {
            $items->attach($items_add->all());
        }
        $status = 'collegati ['.\implode(', ', $items_add->all()).'] scollegati ['.\implode(', ', $items_sub->all()).']';
        \Session::flash('status', $status);
    }

    //*
    public function getXotModel($name) { //spostare in helper ?
        $model = tenantConfig('xra.model.'.$name);
        //ddd($model);
        if (null == $model) {
            return null;
            echo '<h3>not exists ['.$name.'] on config xra.model</h3>';
            ddd(config('xra.model'));
        }
        if ('BaseModel' == class_basename($model)) {
            return abort('403', 'BaseModel is only for Abstract!');
        }
        $row = new $model();

        return $row;
    }

    //*/

    public function detach(Request $request, $container, $item) {
        $item->pivot->delete(); // da aggiungere pivot_id
        $status = 'scollegato';
        \Session::flash('status', $status);

        return Panel::get($item);
    }

    //end detach

    public function destroy(Request $request, $container, $item) {
        $item->delete(); // da aggiungere pivot_id
        $status = 'eliminato';
        \Session::flash('status', $status);

        return Panel::get($item);
    }

    //end detach
}//end class

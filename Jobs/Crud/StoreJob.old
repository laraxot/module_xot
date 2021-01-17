<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
//----------- Requests ----------
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

class StoreJob implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Traits\CommonTrait;

    protected $container;
    protected $item;
    protected $row;
    protected $rows;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($containers, $items, $data = null) {
        if (is_array($containers)) {
            $container = last($containers);
        } else {
            $container = $containers;
            $containers = [$container];
        }
        if (is_array($items)) {
            $item = last($items);
        } else {
            $item = $items;
            $items = [$item];
        }
        $this->container = $container;
        $this->item = $item;
        //dd('['.__LINE__.']['.__FILE__.'][__construct]');
        $types = Str::camel(Str::plural($container));
        if (is_object($item)) {
            $rows = $item->$types();
            $related = $rows->getRelated();
            $row = $related;
        } else {
            $rows = null;
            $row = xotModel($container);
        }
        $this->row = $row;
        if (null == $data) {
            $data = $this->getData();
        }
        //dd('['.__LINE__.']['.__FILE__.'][__construct]');
        $this->data = $data;
        $this->types = $types;
        $this->rows = $rows;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //dd('['.__LINE__.']['.__FILE__.']');
        $row = $this->row;
        $data = $this->data;
        $types = $this->types;
        $item = $this->item;
        //ddd($row, $data, $types, $item);
        //---------------------------
        if (! isset($data['lang']) && in_array('lang', $row->getFillable())) {
            $data['lang'] = app()->getLocale();
        }
        if (! isset($data['auth_user_id']) && in_array('auth_user_id', $row->getFillable())) {
            $data['auth_user_id'] = \Auth::id();
        }

        $row = $row->fill($data);
        $row->save();
        $panel = Panel::get($row);

        if (is_object($item)) {
            $pivot_data = [];
            if (isset($data['pivot'])) {
                $pivot_data = $data['pivot'];
            }
            if (! isset($pivot_data['auth_user_id'])) {
                $pivot_data['auth_user_id'] = \Auth::id();
            }
            try {
                $tmp = $item->$types()->save($row, $pivot_data);
            } catch (\Exception $e) {
                $this->row = $row;
                $func = 'saveParent'.Str::studly(class_basename($item->$types()));
                $tmp = $this->$func();
            }

            //$tmp=$item->$types()->attach($row->getKey(),$pivot_data);
            //$tmp = $item->$types()->save($row, $pivot_data);
        }
        $this->manageRelationships(['model' => $row, 'data' => $data, 'act' => 'store']);
        if (method_exists($panel, 'storeCallback')) {
            $row = $panel->storeCallback(['row' => $row, 'data' => $data]);
        }
        \Session::flash('status', 'creato ! ['.$row->getKey().']!');

        return $panel;
    }

    public function saveParentHasManyDeep() {
    }

    public function saveParentHasManyDeep_OLD() {
        $row = $this->row;
        $data = $this->data;
        $types = $this->types;
        $item = $this->item;

        $rows = $item->$types();
        //debug_getter_obj(['obj'=>$rows]);
        //dddx(get_class_methods($rows));
        $fields = [
            'getQualifiedFarKeyName',
            'getFirstKeyName',
            'getQualifiedFirstKeyName',
            'getForeignKeyName',
            'getQualifiedForeignKeyName',
            'getLocalKeyName',
            'getQualifiedLocalKeyName',
            'getSecondLocalKeyName',
            'getBaseQuery',
            'getParent',
            'getRelated',
            //"getRelationExistenceQuery",
            //"getRelationExistenceQueryForSelfRelation",
            //"getRelationExistenceQueryForThroughSelfRelation",
            'getThroughParents',
            'getForeignKeys',
            'getLocalKeys',
            'getQualifiedParentKeyName',
            //"getMorphedModel",
        ];
        $ris = [];
        $ris['row'] = $row;
        $ris['item'] = $item;
        $ris['types'] = $types;
        $ris['data'] = $data;
        foreach ($fields as $field) {
            $ris[$field] = $rows->$field();
        }
        $ris['item_profiles'] = $item->profiles;
        $ris['row_profile'] = $row->profile;
        $profile_c = collect($item->profiles)->where('post_id', $row->profile->post_id)->count();

        if (0 == $profile_c) {
            //dddx($row->profile);
            //$item->profiles()->associate($row->profile); //Illuminate\Database\Eloquent\Relations\MorphToMany::associate()
            //$item->profiles()->syncWithoutDetaching($row->profile);
            $item->profiles()->save($row->profile);
        }
        /*
        "getQualifiedFarKeyName" => "bell_boys.post_id"
        "getFirstKeyName" => "post_id"
        "getQualifiedFirstKeyName" => "restaurant_morph.post_id"
        "getForeignKeyName" => "post_id"
        "getQualifiedForeignKeyName" => "bell_boys.post_id"
        "getLocalKeyName" => "post_id"
        "getQualifiedLocalKeyName" => "restaurants.post_id"
        "getSecondLocalKeyName" => "post_id"
    */

        //dddx($rows->getParent()); //restaurantMorph
        //dddx($rows->getRelated());//bellboy
        //dddx($rows->getThroughParents());//restaurantMorph , Profile
        //$arr=['bell_boys.auth_user_id'=>$row->auth_user_id];
        //$rows->updateOrCreate($arr);
        //$rows->associate($row); //Call to undefined method Staudenmeir\EloquentHasManyDeep\HasManyDeep::associate()
        //$rows->save($row); //Call to undefined method Staudenmeir\EloquentHasManyDeep\HasManyDeep::save()
        //$rows->sync($row);//Call to undefined method Staudenmeir\EloquentHasManyDeep\HasManyDeep::sync()
        //$rows->attach($row);//Call to undefined method Staudenmeir\EloquentHasManyDeep\HasManyDeep::attach()
        //match??
        //$rows->saveMany([$row]);//Call to undefined method Staudenmeir\EloquentHasManyDeep\HasManyDeep::saveMany()
    }

    //end handle

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
        /*
        $pk = $rows->getRelated()->getKeyName();
        if ('auth_user_id' == $pk) {
            dddx($data);
        }
        */
        //debug_getter_obj(['obj'=>$rows]);
        try {
            $related = $rows->create($data);
        } catch (\Exception $e) {
            //"SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'PRIMARY' (SQL: insert into `liveuser_users` (`first_name`, `last_name`, `email`, `auth_user_id`, `created_by`, `updated_by`, `updated_at`, `created_at`) values (gfdsfs, fdsfds, fds
            //dddx(['e' => $e->getMessage(), 'data' => $data]);
            $related = $rows->update($data);
        }
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

    public function storeRelationshipsMorphOne($params) {
        extract($params);
        if (! isset($data['lang']) /* && in_array('lang', $row->getFillable()) */) {
            $data['lang'] = app()->getLocale();
        }
        if ($model->$name()->exists()) {
            $model->$name()->update($data);
        } else {
            $model->$name()->create($data);
        }
    }

    public function storeRelationshipsMorphToMany($params) {
        extract($params);

        //ddd(\Request::all());
        //return ;

        if (! Arr::isAssoc($data)) {
            $data = collect($data)->map(
                function ($item) use ($model,$name) {
                    if (is_numeric($item)) {
                        return $item;
                    }
                    $related = $model->$name()->getRelated();
                    $related_panel = Panel::get($related);
                    $res = $related_panel->setLabel($item);

                    return $res->getKey().'';
                }
            )->all();
            //dddx($data);
            $model->$name()->sync($data);
        }

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
                $res = $model->$name()->syncWithoutDetaching([$v]);
                /*
                $rows1=$model->$name();
                $related=$rows1->getRelated();
                ddd($related);
                //ddd($params);
                */
                //$model->$name()->attach()
                //ddd('semplice assegnazione');
            }
        }
    }

    public function storeRelationshipsHasManyThrough($params) {
        /*
        Call to undefined method Illuminate\Database\Eloquent\Relations\HasManyThrough::syncWithoutDetaching()
        */
        //$this->storeRelationshipsMorphToMany($params); //
    }

    public function storeRelationshipsBelongsToMany($params) {
        extract($params);
        if (isset($data['from']) || isset($data['to'])) {
            $this->saveMultiselectTwoSides($params);

            return;
        }
        $model->$name()->syncWithoutDetaching($data);
    }

    public function saveMultiselectTwoSides($params) {
        //passo request o direttamente data ?
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
}//end storeJob

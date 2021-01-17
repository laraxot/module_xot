<?php

declare(strict_types=1);

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
//----------- Requests ----------
//------------ services ----------
use Illuminate\Support\Str;
use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class StoreJob.
 */
class StoreJob extends XotBaseJob {
    /**
     * Execute the job.
     */
    public function handle(): PanelContract {
        //dd('['.__LINE__.']['.__FILE__.']');
        $row = $this->panel->row;
        $data = $this->data;

        //---------------------------
        if (! isset($data['lang']) && in_array('lang', $row->getFillable())) {
            $data['lang'] = app()->getLocale();
        }
        if (! isset($data['auth_user_id']) && in_array('auth_user_id', $row->getFillable())) {
            $data['auth_user_id'] = \Auth::id();
        }

        $row = $row->fill($data);
        $row->save();
        $parent = $this->panel->getParent();
        if (is_object($parent)) {
            $parent_row = $parent->row;
            $pivot_data = [];
            if (isset($data['pivot'])) {
                $pivot_data = $data['pivot'];
            }
            if (! isset($pivot_data['auth_user_id'])) {
                $pivot_data['auth_user_id'] = \Auth::id();
            }
            try {
                //$tmp = $parent_row->$types()->save($row, $pivot_data);
                $tmp = $this->panel->rows->save($row, $pivot_data); //??
                /*
                Model
                BelongsToMany
                HasOneOrMany
                */
            } catch (\Exception $e) {
                dddx(['e' => $e]);
                /*
                $this->row = $row;
                $func = 'saveParent'.Str::studly(class_basename($parent_row->$types()));
                $tmp = $this->$func();
                */
            }

            //$tmp=$item->$types()->attach($row->getKey(),$pivot_data);
            //$tmp = $item->$types()->save($row, $pivot_data);
        }

        $this->manageRelationships($row, $data, 'store');
        if (method_exists($this->panel, 'storeCallback')) {
            $row = $this->panel->storeCallback(['row' => $row, 'data' => $data]);
        }
        \Session::flash('status', 'creato ! ['.$row->getKey().']!');

        return $this->panel;
    }

    public function saveParentHasManyDeep() {
    }

    /*
    public function saveParentHasManyDeep_OLD() {
        $row = $this->panel->row;
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

        //"getQualifiedFarKeyName" => "bell_boys.post_id"
        //"getFirstKeyName" => "post_id"
        //"getQualifiedFirstKeyName" => "restaurant_morph.post_id"
        //"getForeignKeyName" => "post_id"
        //"getQualifiedForeignKeyName" => "bell_boys.post_id"
        //"getLocalKeyName" => "post_id"
        //"getQualifiedLocalKeyName" => "restaurants.post_id"
        //"getSecondLocalKeyName" => "post_id"


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
    */

    /**
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsPivot($model, string $name, array $data): void {
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

    /**
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsHasOne($model, string $name, array $data): void {
        $rows = $model->$name();
        $related = $rows->getRelated();

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
            $data = collect($data)->only($related->getFillable())->all();
            //dddx($data);

            $related = $rows->update($data);
            //$rows->fill($data);
            //$related = $rows->save();
        }
        if (! $model->$name()->exists()) {//collegamento non riuscito
            $pk_local = $rows->getLocalKeyName();
            $pk_fore = $rows->getForeignKeyName();
            $data1 = [$pk_local => $related->$pk_fore];
            $model->update($data1);
        }
    }

    /**
     * Undocumented function.
     *
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsHasMany($model, string $name, array $data): void {
        //$rows = $model->$name();
    }

    /**
     * Undocumented function.
     *
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsBelongsTo($model, string $name, array $data): void {
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

    /**
     * Undocumented function.
     *
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsMorphOne($model, string $name, array $data): void {
        if (! isset($data['lang']) /* && in_array('lang', $row->getFillable()) */) {
            $data['lang'] = app()->getLocale();
        }
        if ($model->$name()->exists()) {
            $model->$name()->update($data);
        } else {
            $model->$name()->create($data);
        }
    }

    /**
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsMorphToMany($model, string $name, array $data): void {
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
                    $v['pivot']['auth_user_id'] = \Auth::id();
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

    /**
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsHasManyThrough($model, string $name, array $data): void {
        /*
        Call to undefined method Illuminate\Database\Eloquent\Relations\HasManyThrough::syncWithoutDetaching()
        */
        //$this->storeRelationshipsMorphToMany($params); //
    }

    /**
     * Undocumented function.
     *
     * @param ModelContract|Model $model
     */
    public function storeRelationshipsBelongsToMany($model, string $name, array $data): void {
        if (isset($data['from']) || isset($data['to'])) {
            $this->saveMultiselectTwoSides($model, $name, $data);

            return;
        }
        $model->$name()->syncWithoutDetaching($data);
    }

    /**
     * Undocumented function.
     *
     * @param ModelContract|Model $model
     */
    public function saveMultiselectTwoSides($model, string $name, array $data): void {
        //passo request o direttamente data ?

        $items = $model->$name();
        $related = $items->getRelated();
        //ddd($related);
        $container_obj = $model;
        $container = $container_obj->post_type;
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

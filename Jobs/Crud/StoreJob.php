<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    public function __construct($container, $item, $data = null) {
        $this->container = $container;
        $this->item = $item;

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
        $row = $this->row;
        $data = $this->data;
        $types = $this->types;
        $item = $this->item;
        //ddd($data);
        //---------------------------
        if (! isset($data['lang']) && in_array('lang', $row->getFillable())) {
            $data['lang'] = \App::getLocale();
        }

        $row = $row->fill($data);
        $row->save();
        $panel = Panel::get($row);

        if (is_object($item)) {
            $pivot_data = [];
            if (isset($data['pivot'])) {
                $pivot_data = $data['pivot'];
            }
            //ddd($types);
            //$item->$types()->
            $tmp = $item->$types()->save($row, $pivot_data);
            //$tmp=$item->$types()->sync([$row->getKey()=>$pivot_data]);

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

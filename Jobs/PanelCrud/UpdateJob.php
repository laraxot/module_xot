<?php

namespace Modules\Xot\Jobs\PanelCrud;

use Illuminate\Support\Arr;
//----------- Requests ----------
//------------ services ----------
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Services\PanelService as Panel;

class UpdateJob extends XotBaseJob {
    /**
     * Execute the job.
     *
     * @return PanelContract
     */
    public function handle() {
        $row = $this->panel->row;
        $data = $this->data;
        $ris = $row->update($data);
        $this->manageRelationships($row, $data, 'update');
        \Session::flash('status', 'aggiornato! ['.$row->getKey().']!'); //.implode(',',$row->getChanges())

        return $this->panel;
    }

    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     *--- hasOne ----.
     **/
    public function updateRelationshipsHasOne($model, $name, $data) {
        $rows = $model->$name();
        if ($rows->exists()) {
            if (! is_array($data)) {
                //variabile uguale alla relazione
            } else {
                $model->$name()->update($data);
            }
        } else {
            dddx(['err' => 'wip']);
            //$this->storeRelationshipsHasOne($params);
        }
    }

    /**
     *  belongsTo.
     **/
    public function updateRelationshipsBelongsTo($model, $name, $data) {
        $rows = $model->$name();
        if ($rows->exists()) {
            $model->$name()->update($data);
        //$model->$name->update($data);
        } else {
            //$this->storeRelationshipsBelongsTo($params);
            dddx(['err' => 'wip']);
        }
    }

    /**
     * --- hasMany ---.
     **/
    public function updateRelationshipsHasMany($model, $name, $data) {
        $rows = $model->$name();
        debug_getter_obj(['obj' => $rows]);
        //---------- TO DO ------------//
    }

    //end updateRelationshipsHasMany

    /**
     * --- belongsToMany.
     **/
    public function updateRelationshipsBelongsToMany($model, $name, $data) {
        //$model->$name()->syncWithoutDetaching($data);
        $model->$name()->sync($data);
    }

    //end updateRelationshipsBelongsToMany

    /*    hasOneThrough     */
    /*    hasManyThrough    */
    /*    morphTo           */

    /**
     * morphOne.
     **/
    public function updateRelationshipsMorphOne($model, $name, $data) {
        /* con update or create crea sempre uno nuovo, con update e basta se non esiste non va a crearlo */
        $rows = $model->$name();
        if ($rows->exists()) {
            $rows->update($data);
        } else {
            if (! isset($data['lang'])) {
                $data['lang'] = \App::getLocale();
            }
            $rows->create($data);
        }
    }

    /**
     * morphMany.
     **/
    public function updateRelationshipsMorphMany($model, $name, $data) {
        //$res=$model->$name()->syncWithoutDetaching($data);
        foreach ($data as $k => $v) {
            if (! is_array($v)) {
                $v = [];
            }
            if (! isset($v['pivot'])) {
                $v['pivot'] = [];
            }
            //Call to undefined method Illuminate\Database\Eloquent\Relations\MorphMany::syncWithoutDetaching()
            //$res = $model->$name()->syncWithoutDetaching([$k => $v['pivot']]);
            $model->$name()->touch();
        }
    }

    /**
     * morphToMany.
     **/
    public function updateRelationshipsMorphToMany($model, $name, $data) {
        //dddx([\Request::all(), $params]);
        //$res=$model->$name()->syncWithoutDetaching($data);
        //dddx([$name, Arr::isAssoc($data)]);
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
        //dddx($data);
        //
        //$model->$name()->attach($data);
        //}

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                if (! isset($v['pivot'])) {
                    $v['pivot'] = [];
                }
                //ddd('a');
                /*
                echo '<hr/><pre>'.print_r($v['pivot'],1).'</pre><hr/>';
                $res = $model->$name()
                        ->where('related_id',$k)
                        ->where('auth_user_id',$v['pivot']['auth_user_id'])
                        ->update($v['pivot']);
                */
                $res = $model->$name()
                    ->syncWithoutDetaching([$k => $v['pivot']]);
            } else {
                // $res = $model->$name()
                 //   ->syncWithoutDetaching([$v]);
            }
            //->where('auth_user_id',1)
            //->syncWithoutDetaching([$k => $v['pivot']])

                //->updateOrCreate(['related_id'=>$k,'auth_user_id'=>1],$v['pivot']);
            //$model->$name()->touch();
        }
    }

    /**
     * morphedByMany.
     **/

    /**
     * pivot.
     **/
    public function updateRelationshipsPivot($model, $name, $data) {
        $model->$name->update($data);
    }

    public function saveMultiselectTwoSides($model, $name, $data) {
        $items = $model->$name();
        $related = $items->getRelated();
        $container_obj = $model;
        $container = $container_obj->post_type;
        //$items_key = $container_obj->getKeyName();
        $items_key = $related->getKeyName();
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
}

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

//--- to do ---

class IndexUpdateJob implements ShouldQueue {
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

        if (! is_object($item)) {
            $row = xotModel($container);
            $rows = $row;
        } else {
            $types = Str::camel(Str::plural($container));
            $rows = $item->$types();
            $row = $rows->getRelated();
        }
        $this->row = $row;
        $this->rows = $rows;
        $this->panel = Panel::get($row);
        $this->panel->setRows($rows);
        //ddd($this->panel);

        if (null == $data) {
            $data = $this->getData();
        }
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //ddd($this->item->groups);
        $this->manageRelationships(['model' => $this->item, 'data' => $this->data, 'act' => 'indexUpdate']);

        return $this->panel;
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
}

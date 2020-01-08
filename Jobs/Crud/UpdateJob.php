<?php

namespace Modules\Xot\Jobs\Crud;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

//----------- Requests ----------
use Modules\Xot\Http\Requests\XotRequest;
//------------ services ----------
use Modules\Xot\Services\PanelService as Panel;

class UpdateJob implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Traits\CommonTrait;

    protected $container;
    protected $item;
    protected $row;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($container,$item,$data=null){
        $this->container=$container;
        $this->item=$item;
        $this->row=$item;
        if($data==null){
            $data=$this->getData();
        }
        $this->data=$data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $row=$this->row;
        $data=$this->data;
        //--
        $panel=Panel::get($row);
        $ris = $row->update($data);
        $this->manageRelationships(['model' => $row, 'data' => $data, 'act' => 'update']);
         \Session::flash('status', 'aggiornato! ['.$row->getKey().']!'); //.implode(',',$row->getChanges())
        return $panel;
    }
    /**
    *--- hasOne ----    
    **/
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
    /**
    *  belongsTo
    **/
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
    /**
    * --- hasMany ---
    **/
    public function updateRelationshipsHasMany($params) {
        extract($params);
        $rows = $model->$name();
        debug_getter_obj(['obj'=>$rows]);
        //---------- TO DO ------------//
    }//end updateRelationshipsHasMany

    /** 
    * --- belongsToMany 
    **/
    public function updateRelationshipsBelongsToMany($params) {
        extract($params);
        if (isset($data['from']) || isset($data['to'])) {
            $this->saveMultiselectTwoSides($params);
            return ;
        } 
        $model->$name()->syncWithoutDetaching($data);
    }//end updateRelationshipsBelongsToMany


    /*    hasOneThrough     */
    /*    hasManyThrough    */
    /*    morphTo           */
    /**    
    * morphOne          
    **/
    public function updateRelationshipsMorphOne($params) {
        extract($params);
        $model->$name()->update($data);
    }
    /**    
    * morphMany         
    **/

    /**    
    * morphToMany       
    **/
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
    /**    
    * morphedByMany     
    **/

    /**  
    * pivot 
    **/
    public function updateRelationshipsPivot($params) {
        extract($params);
        $model->$name->update($data);
    }

    

    






}

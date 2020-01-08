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


//--- to do ---

class IndexUpdateJob implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
    public function __construct($container,$item,$data=null){
        $this->container=$container;
        $this->item=$item;
        
        if(!is_object($item)){
            $row = xotModel($container);
            $rows = $row;
        }else{
            $types = Str::camel(Str::plural($container));
            $rows = $item->$types();
            $row = $rows->getRelated();
        }
        $this->row=$row;
        $this->rows=$rows;
        $this->panel=Panel::get($row);
        $this->panel->setRows($rows);
       //ddd($this->panel);
        /*
        if($data==null){
            //$data=$this->getData();
        }
        $this->data=$data;
        */
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
       

        return $this->panel;
    }
}
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

class destroyJob implements ShouldQueue{
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
        $this->row=$item;
        $this->panel=Panel::get($this->row);
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $this->row->delete();
        \Session::flash('status', 'eliminato');
        return $this->panel;
    }
    
}
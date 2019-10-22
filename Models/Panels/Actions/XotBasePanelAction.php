<?php

namespace Modules\Xot\Models\Panels\Actions;

//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------

//---- Traits ----
//use Modules\Xot\Traits\Updater;

abstract class XotBasePanelAction {
    /*
    abstract public function setRows($rows);
    public function btn();
    public function handle();
    */

    public function setRows($rows){
    	$this->rows=$rows;
    }

    public function setRow($row){
    	$this->row=$row;
    }
}

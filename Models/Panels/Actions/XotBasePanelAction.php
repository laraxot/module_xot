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
    public function btn($params=[]);
    */
    abstract public function handle();

    public function setRows($rows){
    	$this->rows=$rows;
    }

    public function setRow($row){
    	$this->row=$row;
    }

    public function btn($params=[]){

        if($this->onContainer){
            return $this->btnContainer($params);
        }
        
        return $this->btnItem($params);
    }

    public function btnContainer($params=[]){
        $request=\Request::capture();
        $title=$this->name;
        $title=str_replace('_',' ',$title);
        $url=$request->fullUrlWithQuery(['_act'=>$this->name]);
        return '<a href="'.$url.'" class="btn btn-secondary" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'&nbsp;'.$title.'
            </a>';
    }//end btnContainer

    public function btnItem($params=[]){
        extract($params);
        $url=RouteService::urlAct(
            [
                'row'=>$row,
                'act'=>'show',
                'query'=>[
                    '_act'=>$this->name,
                    //'stato'=>6,
                ],
            ]);
        return '<a href="'.$url.'" class="btn btn-success">
            <i class="fas fa-file-signature"></i>&nbsp;Compila
            </a>';
    }//end btnItem

}

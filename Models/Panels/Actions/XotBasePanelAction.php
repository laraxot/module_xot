<?php

namespace Modules\Xot\Models\Panels\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------
use Modules\Xot\Services\RouteService;
use Modules\Xot\Services\ArrayService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Theme\Services\ThemeService;
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
        $title=str_replace('_',' ',$this->name);
        return '<a href="'.$url.'" class="btn btn-success" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'</i>&nbsp;'.$title.'
            </a>';
    }//end btnItem

    public function updateRow(){
        $row=$this->row;
        $panel=Panel::get($row);
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $request->validatePanel($panel);
        $data=$request->all();
        $res=tap($row)->update($data);
        \Session::flash('status', 'aggiornato! ['.$row->getKey().']!'); 
        return $this->handle();
    }

}

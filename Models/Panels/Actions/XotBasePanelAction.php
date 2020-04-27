<?php

namespace Modules\Xot\Models\Panels\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------
use Modules\Xot\Jobs\Crud\UpdateJob;
use Modules\Xot\Services\PanelService as Panel;
//------------ jobs ----------------------------
use Modules\Xot\Services\RouteService;

//---- Traits ----
//use Modules\Xot\Traits\Updater;

abstract class XotBasePanelAction {
    /*
    abstract public function setRows($rows);
    public function btn($params=[]);
    */
    public $onContainer = false;
    public $onItem = false;
    public $row = null;
    public $rows = null;
    public $name = null;

    abstract public function handle();

    public function getName() {
        if (null != $this->name) {
            return $this->name;
        }
        $this->name = Str::snake(class_basename($this));
        $str = '_action';
        if (Str::endsWith($this->name, $str)) {
            $this->name = Str::before($this->name, $str);
        }

        return $this->name;
    }

    public function getTitle() {
        $title = $this->getName();
        $title = str_replace('_', ' ', $title);

        return $title;
    }

    public function setRows($rows) {
        $this->rows = $rows;
    }

    public function setRow($row) {
        $this->row = $row;
    }

    public function btn($params = []) {
        extract($params);
        if (isset($row)) {
            $this->setRow($row);
        }
        if (isset($this->onItem) && $this->onItem && is_object($this->row)) {
            return $this->btnItem($params);
        }

        return $this->btnContainer($params);
        /*
        if ($this->onContainer) {
            return $this->btnContainer($params);
        }
        return $this->btnItem($params);
        */
    }

    public function urlContainer($params = []) {
        extract($params);
        $request = \Request::capture();
        $name = $this->getName();
        $url = $request->fullUrlWithQuery(['_act' => $name]);
        if (isset($panel)) {
            $url = $panel->indexUrl();
            $url = url_queries(['_act' => $name], $url);
        }

        return $url;
    }

    public function btnContainer($params = []) {
        $url = $this->urlContainer($params);
        $title = $this->getTitle();

        return '<a href="'.$url.'" class="btn btn-secondary" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'&nbsp;'.$title.'
            </a>';
    }

    //end btnContainer
    public function urlItem($params = []) {
        //dddx($params);
        extract($params);
        $this->setRow($row);
        $name = $this->getName();
        //$url = RouteService::urlPanel(['panel' => $panel, 'act' => 'show']);
        //*
        try {
            $url = RouteService::urlAct(
                [
                'row' => $this->row,
                'act' => 'show',
                'query' => [
                    '_act' => $name,
                    //'stato'=>6,
                ],
            ]
            );
        }catch(\Exception $e){
            dddx(['e'=>$e->getMessage()]);
        }
        //*/
        return $url;
    }

    public function btnItem($params = []) {
        /*
        extract($params);
        $name = $this->getName();

        $url = RouteService::urlAct(
            [
                'row' => $row,
                'act' => 'show',
                'query' => [
                    '_act' => $name,
                    //'stato'=>6,
                ],
            ]);
        */
        $url = $this->urlItem($params);
        $title = $this->getTitle();
        $method = Str::camel($this->getName());

        if (Gate::allows($method, $this->row)) {
            return '<a href="'.$url.'" class="btn btn-success" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'</i>&nbsp;'.$title.'
            </a>';
        } else {
            return '<button type="button" class="btn btn-secondary btn-lg" data-toggle="tooltip" title="not can" disabled>'.$this->icon.' '.$method.'</button>';
        }
    }

    //end btnItem

    public function updateRow($params = []) {
        $row = $this->row;
        $container = null;
        extract($params);

        $item = $row;
        //$item=\Modules\Food\Models\Restaurant::first();
        $up = UpdateJob::dispatchNow($container, $item);
        //try{
        //$tmp=new UpdateJob($container, $item);
        //}catch(\Exception $e){
        //debug_getter_obj(['obj'=>$e]);
        //ddd($e->getMessage());
        //ddd($e->errors());
        /*
        ValidationException {#1787 ▼
            +validator: Validator {#1784 ▶}
            +response: null
            +status: 422
            +errorBag: "default"
            +redirectTo: null
            #message: "The given data was invalid."
            #code: 0
            #file: "C:\var\www\multi\laravel\vendor\laravel\framework\src\Illuminate\Validation\Validator.php"
            #line: 315
            trace: {▶}
          }
          */
        // $up->set
        //}

        return $up;
        //ddd($up);
        /*
        $panel = Panel::get($row);
        $request = \Modules\Xot\Http\Requests\XotRequest::capture();
        $request->validatePanel($panel);
        $data = $request->all();

        $res = tap($row)->update($data);
        */
        //--- manca update relationship !
        //----
        //\Session::flash('status', 'aggiornato! ['.$row->getKey().']!');

        //return $this->handle();
    }

    public function pdf($params = []) {
        if (null == $this->row) {
            $this->row = clone($this->rows)->get()[0];
            if (! is_object($this->row)) {
                die('<h3>Nulla da creare nel PDF</h3>');
                //$this->row=$this->rows->getModel();
            }
        }
        $panel = Panel::get($this->row);
        $panel->setRows($this->rows);

        return $panel->pdf($params);
    }
}

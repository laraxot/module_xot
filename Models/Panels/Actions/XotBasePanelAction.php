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
    public $row=null;
    public $rows=null;
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
        if(isset($row)){
            $this->setRow($row);
        }
        if(isset($this->onItem) && $this->onItem && is_object($this->row)){
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
        $request = \Request::capture();
        $name = $this->getName();
        $url = $request->fullUrlWithQuery(['_act' => $name]);

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
        extract($params);
        $this->setRow($row);
        $name = $this->getName();
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
        $method=Str::camel($this->getName());

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
        extract($params);
        $container = null;
        $item = $row;
        UpdateJob::dispatch($container, $item);
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

        return $this->handle();
    }
}

<?php

namespace Modules\Xot\Models\Panels\Actions;

//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\RouteService;

//------------ jobs ----------------------------
use Modules\Xot\Jobs\Crud\UpdateJob;

//---- Traits ----
//use Modules\Xot\Traits\Updater;

abstract class XotBasePanelAction {
    /*
    abstract public function setRows($rows);
    public function btn($params=[]);
    */
    abstract public function handle();

    public function setRows($rows) {
        $this->rows = $rows;
    }

    public function setRow($row) {
        $this->row = $row;
    }

    public function btn($params = []) {
        if ($this->onContainer) {
            return $this->btnContainer($params);
        }

        return $this->btnItem($params);
    }

    public function btnContainer($params = []) {
        $request = \Request::capture();
        $title = $this->name;
        $title = str_replace('_', ' ', $title);
        $url = $request->fullUrlWithQuery(['_act' => $this->name]);

        return '<a href="'.$url.'" class="btn btn-secondary" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'&nbsp;'.$title.'
            </a>';
    }

    //end btnContainer

    public function btnItem($params = []) {
        extract($params);
        $url = RouteService::urlAct(
            [
                'row' => $row,
                'act' => 'show',
                'query' => [
                    '_act' => $this->name,
                    //'stato'=>6,
                ],
            ]);
        $title = str_replace('_', ' ', $this->name);

        return '<a href="'.$url.'" class="btn btn-success" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'</i>&nbsp;'.$title.'
            </a>';
    }

    //end btnItem

    public function updateRow($params=[]) {
        $row = $this->row;
        extract($params);
        $container=null;
        $item=$row;
        UpdateJob::dispatch($container,$item);
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

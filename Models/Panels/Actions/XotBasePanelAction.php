<?php

namespace Modules\Xot\Models\Panels\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------
use Modules\FormX\Services\FormXService;
use Modules\Xot\Jobs\Crud\UpdateJob;
//------------ jobs ----------------------------
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\RouteService;

abstract class XotBasePanelAction {
    public $onContainer = false;
    public $onItem = false;
    public $row = null;
    public $rows = null;
    public $panel = null;
    public $name = null;
    public $icon = '<i class="far fa-question-circle"></i>';
    public $class = 'btn btn-secondary mb-2';
    protected $data = [];
    public $related = null; //post_type per filtrare le azioni nei vari index_edit

    abstract public function handle();

    /*
    public function __construct() {
        parent::__construct();
    }
    */

    public function __construct() {
        $data = request()->all();
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    public function getName() {
        if (Str::contains($this->name, '::')) {
            $this->name = null;
        }
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
        $name = $this->getName();

        $row = $this->panel->row;

        $module_name_low = strtolower(getModuleNameFromModel($row));
        $trans_path = $module_name_low.'::'.strtolower(class_basename($row)).'.act.'.$name;
        $trans = trans($trans_path);

        if ($trans_path == $trans && ! config('xra.show_trans_key')) {
            $title = str_replace('_', ' ', $name);
        } else {
            $title = $trans;
        }

        return $title;
    }

    public function getUrl($params = []) {
        if (isset($this->onItem) && $this->onItem) {
            return $this->urlItem($params);
        }

        return $this->urlContainer($params);
    }

    public function setRows($rows) {
        $this->rows = $rows;
    }

    public function setRow($row) {
        $this->row = $row;
    }

    public function setPanel($panel) {
        $this->panel = $panel;
    }

    public function btn($params = []) {
        extract($params);
        if (isset($row)) {
            $this->setRow($row);
        }
        if (isset($panel)) {
            $this->setPanel($panel);
        }
        if (isset($this->onItem) && $this->onItem) {
            return $this->btnItem($params);
        }

        return $this->btnContainer($params);
    }

    public function url($params = []) {
        if (isset($this->onItem) && $this->onItem) {
            return $this->urlItem($params);
        }

        return $this->urlContainer($params);
    }

    public function urlContainer($params = []) {
        $panel = $this->panel;
        extract($params);
        $request = \Request::capture();
        $name = $this->getName();
        //$url = $request->fullUrlWithQuery(['_act' => $name]);
        //if (isset($panel)) {
        //dddx([request()->all(),$this->data]);
        //dddx(request()->all());
        //dddx(get_class_methods(request()));

        $this->data = array_merge(request()->query(), $this->data);
        //$this->data = collect($this->data)->except(['fingerprint', 'serverMemo', 'updates'])->all();

        $url = $panel->indexUrl();
        $url = url_queries(['_act' => $name], $url);
        $this->data['page'] = 1;
        $this->data['_act'] = $name;
        $url = url_queries($this->data, $url);
        //}

        return $url;
    }

    public function with($key, $value = null) {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function btnHtml($params = []) {
        $params['panel'] = $this->panel;
        $params['url'] = $this->getUrl($params);

        if (isset($params['debug']) && true === $params['debug']) {
            dddx($params);
        }

        $params['method'] = Str::camel($this->getName());
        if (! isset($params['act'])) {
            if ($this->onContainer) {
                $params['act'] = 'index';
            } else {
                $params['act'] = 'show';
            }
        }
        if (! isset($params['title'])) {
            $params['title'] = ''; // $this->getTitle();
        }
        if (true === $params['title']) {
            $params['title'] = $this->getTitle();
        }

        if (! isset($params['tooltip'])) {
            $params['tooltip'] = $this->getTitle();
        }

        if (! isset($params['data_title'])) {
            $params['data_title'] = $this->getTitle();
        }
        if (! isset($params['icon'])) {
            $params['icon'] = $this->icon;
        }
        if (! isset($params['class'])) {
            $params['class'] = $this->class;
        }

        return FormXService::btnHtml($params);
    }

    public function btnContainer($params = []) {
        $url = $this->urlContainer($params);
        $title = $this->getTitle();
        $params['url'] = $url;
        $params['title'] = $title;

        return $this->btnHtml($params);
        /*'<a href="'.$url.'" class="btn btn-secondary" data-toggle="tooltip" title="'.$title.'">
            '.$this->icon.'&nbsp;'.$title.'
            </a>';*/
    }

    //end btnContainer
    public function urlItem($params = []) {
        //dddx($params);
        $query_params = [];
        extract($params);
        if (isset($row)) {
            $this->setRow($row);
        }
        if (! isset($this->panel)) {
            $this->panel = Panel::get($this->row);
        }
        $name = $this->getName();
        $url = RouteService::urlPanel(['panel' => $this->panel, 'act' => 'show']);
        $query_params['_act'] = $name;
        /*
        if (isset($modal)) {
            $this->data['modal'] = $modal;
        }
        $this->data = array_merge(request()->all(), $this->data);
        */

        $url = url_queries($query_params, $url);
        $url = url_queries($this->data, $url);

        return $url;
    }

    public function btnItem($params = []) {
        $url = $this->urlItem($params);
        $title = $this->getTitle();
        $method = Str::camel($this->getName());

        extract($params);
        if (Gate::allows($method, $this->panel)) {
            if (isset($modal)) {
                switch ($modal) {
                    case 'iframe':
                        return
                        '<button type="button" data-title="'.$title.'"
						data-href="'.$url.'" data-toggle="modal" class="btn btn-secondary mb-2" data-target="#myModalIframe">
                        '.$this->$icon.'
                        </button>';
                    break;
                    case 'ajax':
                    break;
                }
            }

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

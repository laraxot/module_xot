<?php

namespace Modules\Xot\Models\Panels\Actions;

use ErrorException;
use Exception;
use Illuminate\Http\Request;
//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------
use Illuminate\Support\Facades\Gate;
//------------ jobs ----------------------------
use Illuminate\Support\Str;
use Modules\FormX\Services\FormXService;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePanelAction
 * @package Modules\Xot\Models\Panels\Actions
 */
abstract class XotBasePanelAction {
    /**
     * @var bool
     */
    public bool $onContainer = false;
    /**
     * @var bool
     */
    public bool $onItem = false;
    /**
     * @var null
     */
    public $row = null;
    /**
     * @var null
     */
    public $rows = null;
    /**
     * @var null
     */
    public $panel = null;
    /**
     * @var null
     */
    public string $name = null;
    /**
     * @var string
     */
    public string $icon = '<i class="far fa-question-circle"></i>';
    /**
     * @var string
     */
    public string $class = 'btn btn-secondary mb-2';
    /**
     * @var array
     */
    protected array $data = [];
    /**
     * @var null
     */
    public string $related = null; //post_type per filtrare le azioni nei vari index_edit

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

    /**
     * @param $panel
     */
    public function setPanel(&$panel) {
        $this->panel = $panel;
    }

    /**
     * @return string|null
     */
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

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|string[]|null
     */
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

    /**
     * @param array $params
     * @return string
     */
    public function getUrl($params = []) {
        if (isset($this->onItem) && $this->onItem) {
            return $this->urlItem($params);
        }

        return $this->urlContainer($params);
    }

    /**
     * @param $rows
     */
    public function setRows($rows) {
        $this->rows = $rows;
    }

    /**
     * @param $row
     */
    public function setRow($row) {
        $this->row = $row;
    }

    /**
     * @param array $params
     * @return string|void|null
     */
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

    /**
     * @param array $params
     * @return string
     */
    public function url($params = []) {
        if (isset($this->onItem) && $this->onItem) {
            return $this->urlItem($params);
        }

        return $this->urlContainer($params);
    }

    /**
     * @param array $params
     * @return string
     */
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

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function with($key, $value = null) {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param array $params
     * @return string|void|null
     */
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

    /**
     * @param array $params
     * @return string|void|null
     */
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

    /**
     * @param array $params
     * @return string
     */
    public function urlItem($params = []) {
        //dddx($params);
        $url = '';
        $query_params = [];
        extract($params);
        if (isset($row)) {
            $this->setRow($row);
        }
        if (! isset($this->panel)) {
            $this->panel = Panel::get($this->row);
        }
        $name = $this->getName();
        try {
            $url = $this->panel->route->urlPanel(['act' => 'show']);
        } catch (Exception $e) {
            dddx($e->getMessage());
        }/* catch (ErrorException $e) {
            dddx($e->getMessage());
        }*/
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

    /**
     * @param array $params
     * @return string
     */
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
                        '.$this->icon.'
                        </button>';
                    //break;
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
    /*
    public function updateRow($params = []) {
        $row = $this->row;
        $container = null;
        extract($params);

        $item = $row;
        $up = UpdateJob::dispatchNow($container, $item);
        return $up;
    }
    */

    /**
     * @param array $params
     * @return mixed
     */
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

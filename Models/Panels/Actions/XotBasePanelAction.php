<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels\Actions;

use ErrorException;
use Exception;
use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\Model;
//use Laravel\Scout\Searchable;

//----------  SERVICES --------------------------
use Illuminate\Database\Eloquent\Collection;
//------------ jobs ----------------------------
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Modules\FormX\Services\FormXService;
use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Services\PanelService as Panel;

/**
 * Class XotBasePanelAction.
 */
abstract class XotBasePanelAction {
    public bool $onContainer = false;

    public bool $onItem = false;

    /**
     * @var ModelContract|Model
     */
    public $row;

    /**
     * @var Collection|ModelContract[]|Builder|null
     */
    public $rows = null;

    public PanelContract $panel;

    public ?string $name = null;

    public string $icon = '<i class="far fa-question-circle"></i>';

    public string $class = 'btn btn-secondary mb-2';

    protected array $data = [];

    public ?string $related = null; //post_type per filtrare le azioni nei vari index_edit

    /**
     * handle.
     *
     * @return mixed
     */
    abstract public function handle();

    /*
    public function __construct() {
        parent::__construct();
    }
    */
    /*
    public function __construct() {
        $data = request()->all();
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
    */

    public function setPanel(PanelContract &$panel): self {
        /*
        if (null == $panel) {
            throw new Exception('panel is null');
        }
        */
        $this->panel = $panel;

        return $this;
    }

    public function getName(): string {
        if (Str::contains((string) $this->name, '::')) {
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
     *
     * @return string
     */
    public function getUrl(array $params = []) {
        if (isset($this->onItem) && $this->onItem) {
            return $this->urlItem($params);
        }

        return $this->urlContainer($params);
    }

    /**
     * @param object $rows
     */
    public function setRows($rows): self {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @param object $row
     */
    public function setRow($row): self {
        $this->row = $row;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return string|void|null
     */
    public function btn(array $params = []) {
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
     *
     * @return string
     */
    public function url(array $params = []) {
        if (isset($this->onItem) && $this->onItem) {
            return $this->urlItem($params);
        }

        return $this->urlContainer($params);
    }

    public function urlContainer(array $params = []): string {
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

        //$url = $panel->indexUrl();
        $url = $panel->url(['act' => 'index']);
        $url = url_queries(['_act' => $name], $url);
        $this->data['page'] = 1;
        $this->data['_act'] = $name;
        $url = url_queries($this->data, $url);
        //}

        return $url;
    }

    /**
     * @param array|string $key
     * @param mixed        $value
     */
    public function with($key, $value = null): self {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @return string|void|null
     */
    public function btnHtml(array $params = []) {
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
     * @return string|void|null
     */
    public function btnContainer(array $params = []) {
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
     * @return string
     */
    public function urlItem(array $params = []) {
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
     * @return string
     */
    public function btnItem(array $params = []) {
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
    //* --
    public function updateRow(array $params = []): PanelContract {
        $row = $this->row;
        extract($params);
        $this->panel->setRow($row);
        $data = request()->all();
        $this->panel->update($data);

        return $this->panel;
        /*
        $item = $row;
        $up = UpdateJob::dispatchNow($container, $item);
        return $up;
        */
    }

    //*/

    public function pdf(array $params = []) {
        /*
        if (null == $this->row) {
            $this->row = clone($this->rows)->get()[0];
            if (! is_object($this->row)) {
                die('<h3>Nulla da creare nel PDF</h3>');
                //$this->row=$this->rows->getModel();
            }
        }
        $panel = Panel::get($this->row);
        $panel->setRows($this->rows);
        */
        return $this->panel->pdf($params);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 3c97c308c85924a62f31c89c71edfe23450749f0

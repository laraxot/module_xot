<?php

namespace Modules\Xot\Models\Panels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
//----------  SERVICES --------------------------
use Illuminate\Support\Str;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Services\ChainService;
use Modules\Xot\Services\HtmlService;
use Modules\Xot\Services\ImageService;
use Modules\Xot\Services\NavService;
use Modules\Xot\Services\PanelFormService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\PanelTabService;
use Modules\Xot\Services\RouteService;
use Modules\Xot\Services\StubService;

abstract class XotBasePanel /*implements PanelContract*/
{
    public $out = null;
    public $force_exit = false;
    public $msg = 'msg from panel';
    public $row = null;
    public $rows = null;
    public $parent = null;
    //protected static $model;

    public function __construct(PanelPresenterContract $presenter) {
        //$this->row = $model;
        $this->presenter = $presenter;
        $this->presenter->setPanel($this);
    }

    public function setRow($row) {
        $this->row = $row;

        return $this;
    }

    public function setRows($rows) {
        $this->rows = $rows;

        return $this;
    }

    public function initRows() {
        $this->rows = $this->rows();
        $this->rows = $this->rows;

        return $this;
    }

    public function setParent($parent) {
        $this->parent = $parent;

        return $this;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getParents() {
        /*
        $curr = $this;
        $parents = [];
        while (null != $curr) {
            $parents[] = $curr;
            $curr = $curr->getParent();
        }

        return $parents;
        */

        $parents = collect([]);
        $panel_curr = $this;

        while (null != $panel_curr->getParent()) {
            $parents->prepend($panel_curr->getParent());
            $panel_curr = $panel_curr->getParent();
        }

        return $parents;
    }

    public function findParentType($type) {
        return collect($this->getParents())->filter(
            function ($item) use ($type) {
                return $type == $item->postType();
            }
        )->first();
    }

    public function optionId($row) {
        return $row->getKey();
    }

    public function setItem($guid) {
        $model = $this->row;
        $rows = $this->rows;
        $pk = $model->getRouteKeyName();
        $pk_full = $model->getTable().'.'.$pk;

        if ('guid' == $pk) {
            $pk_full = 'guid';
        } // pezza momentanea

        $value = Str::slug($guid); //retrocompatibilita'
        if ('guid' == $pk_full) {
            $rows = $rows->whereHas('post', function (Builder $query) use ($value) {
                $query->where('guid', $value);
            });
        } else {
            $rows = $rows->where([$pk_full => $value]);
        }
        $this->row = $rows->first();
    }

    //funzione/flag da settare a true ad ogni pannello/modello che abbia le traduzioni
    public function hasLang() {
        return false;
    }

    public function setLabel($label) {
        $model = $this->row;
        $res = $model::whereHas(
            'post',
            function (Builder $query) use ($label) {
                $query->where('title', 'like', $label);
            }
        )->first();
        if (is_object($res)) {
            return $res;
        }
        $me = $model->create();
        // dddx([$me, $me->getKey()]);
        $post = $model->post()->create(
            [
                //'post_id' => $me->getKey(),
                'title' => $label,
                'lang' => \App::getLocale(),
            ]
        );
        if (null == $post->post_id) {
            $post->post_id = $me->getKey();
            $post->save();
        }

        return $me;
    }

    /**
     * on select the option label.
     */
    public function optionLabel($row) {
        return $row->matr.' ['.$row->email.']['.$row->ha_diritto.'] '.$row->cognome.' '.$row->cognome.' ';
    }

    public function optionsSelect() {
        $opts = [];
        $rows = $this->rows;
        if (null == $rows) {
            $rows = $this->options();
        }

        foreach ($rows as $row) {
            $id = $this->optionId($row);
            $label = $this->optionLabel($row);

            $opts[$id] = $label;
        }

        return $opts;
    }

    public function options($data = null) {
        if (null == $data) {
            $data = request()->all();
        }

        return $this->rows($data)->get();
    }

    public function optionsTree($data = null) {
        if (null == $data) {
            $data = request()->all();
        }
        $rows = $this->rows($data)->get();
        $primary_field = $this->row->getKeyName();
        $c = new ChainService($primary_field, 'parent_id', 'pos', $rows);
        $options = collect($c->chain_table)->map(
            function ($item) {
                $label = str_repeat('------', $item->indent + 1).$this->optionLabel($item);

                return [
                    'id' => $this->optionId($item),
                    'label' => $label,
                ];
            }
        )->pluck('label', 'id')
            ->prepend('Root', 0)
            ->all();

        return $options;
    }

    public function optionIdName() {
        return $this->row->getKeyName();
    }

    public function optionLabelName() {
        return 'matr';
    }

    public function search() {
        return [];
    }

    public function orderBy() {
        return [];
    }

    /*
    public function fields(){
        return [];
    }
    //*/

    public function rules($params = []) {
        $act = '';
        extract($params);
        if ('' == $act) {
            $route_action = \Route::currentRouteAction();
            $act = Str::after($route_action, '@');
        }
        switch ($act) {
            case 'store':
                $fields = $this->createFields();
                break;
            case 'update':
                $fields = $this->editFields();
                break;
            default:
                $fields = $this->fields();
                break;
        }
        $act = request()->input('_act');
        if ('' != $act) {
            $fields = collect($fields)->filter(
                function ($item) use ($act) {
                    if (! isset($item->except)) {
                        $item->except = [];
                    }

                    return ! in_array($act, $item->except);
                }
            )->all();
        }

        //ddd($fields);

        $rules = collect($fields)->map(
            function ($item) {
                if (! isset($item->rules)) {
                    $item->rules = '';
                }
                if ('pivot_rules' == $item->rules) {
                    $rel_name = $item->name;
                    $pivot_class = with(new $this::$model())
                        ->$rel_name()
                        ->getPivotClass();
                    $pivot = new $pivot_class();
                    $pivot_panel = StubService::getByModel($pivot, 'panel', true);
                    //ddd('preso ['.$pivot_class.']['.get_class($pivot_panel).']');
                    $pivot_rules = collect($pivot_panel->rules())
                        ->map(
                            function ($pivot_rule_val, $pivot_rule_key) use ($item) {
                                $k = $item->name.'.*.pivot.'.$pivot_rule_key;

                                return [$k => $pivot_rule_val];
                            }
                        )->collapse()->all();

                    return $pivot_rules;
                }

                return [$item->name => $item->rules];
            }
        )->collapse()
            ->all();

        return $rules;
    }

    public function pivotRules($params) {
        extract($params);
    }

    public function rulesMessages() {
        $lang = app()->getLocale();
        $rules_msg_fields = collect($this->fields())->filter(function ($value, $key) use ($lang) {
            return isset($value->rules_messages) && isset($value->rules_messages[$lang]);
        })
            ->map(function ($item) use ($lang) {
                $tmp = [];
                /*
            * togliere la lang dai messaggi usare la stringa come id di validazione
            * se la traduzione non esiste, restituire la stringa normale
            **/
                foreach ($item->rules_messages[$lang] as $k => $v) {
                    $tmp[$item->name.'.'.$k] = $v;
                }

                return $tmp;
            })
            ->collapse()
            ->all();
        $mod = Str::before(Str::after(static::$model, 'Modules\\'), '\\');
        $mod = strtolower($mod);
        $name = Str::snake(class_basename(static::$model));
        $trans_ns = $mod.'::'.$name.'__rules_messages';
        //ddd($trans_ns);//food::restaurant_owner__rules_messages
        $rules_msg = trans($trans_ns);
        if (! \is_array($rules_msg)) {
            $rules_msg = [];
        }
        $rules_msg_generic = trans('theme::generic');
        if (! \is_array($rules_msg_generic)) {
            $rules_msg_generic = [];
        }
        $msg = [];
        //$msg = \array_merge($msg,$rules_msg_generic);
        //$msg = \array_merge($msg, $rules_msg);
        $msg = \array_merge($msg, $rules_msg_fields);

        return $msg;
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function filters(Request $request = null) {
        return [];
    }

    public function getXotModelName() {
        return collect(config('xra.model'))->search(static::$model);
    }

    public function indexNav() {
        return null;
    }

    public function getActions($params = []) {
        $panel = $this;
        $filters = [];
        extract($params);
        $actions = collect($this->actions())->filter(
            function ($item) use ($panel, $filters) {
                $item->getName();
                $res = true;
                foreach ($filters as $k => $v) {
                    if (! isset($item->$k)) {
                        $item->$k = false;
                    }
                    if ($item->$k != $v) {
                        return false;
                    }
                }

                return $res;
            }
        )->map(
            function ($item) use ($panel) {
                $item->setPanel($panel);

                return $item;
            }
        );

        return $actions;
    }

    public function containerActions($params = []) {
        $params['filters']['onContainer'] = true;

        return $this->getActions($params);
    }

    public function itemActions($params = []) {
        $params['filters']['onItem'] = true;

        return $this->getActions($params);
    }

    public function itemAction($act) {
        $itemActions = $this->itemActions();
        $itemAction = $itemActions->firstWhere('name', $act);
        if (! is_object($itemAction)) {
            dddx([
                'error' => 'nessuna azione con questo nome',
                'act' => $act,
                'this' => $this,
                'itemActions' => $itemActions,
            ]);
        }
        $itemAction->setPanel($this);

        return $itemAction;
    }

    public function containerAction($act) {
        $actions = $this->containerActions();
        $action = $actions->firstWhere('name', $act);
        if (! is_object($action)) {
            dddx([
                'error' => 'nessuna azione con questo nome',
                'act' => $act,
                'this' => $this,
                'Actions' => $actions,
            ]);
        }
        $action->setPanel($this);

        return $action;
    }

    public function urlContainerAction($act) {
        $containerActions = $this->containerActions();
        $containerAction = $containerActions->firstWhere('name', $act);
        if (is_object($containerAction)) {
            return $containerAction->urlContainer(['rows' => $this->rows, 'panel' => $this]);
        }
    }

    public function urlItemAction($act) {
        //$itemActions = $this->itemActions();
        //$itemAction = $itemActions->firstWhere('name', $act);
        $itemAction = $this->itemAction($act);
        if (is_object($itemAction)) {
            return $itemAction->urlItem(['row' => $this->row, 'panel' => $this]);
        }
    }

    public function btnItemAction($act) {
        //$itemActions = $this->itemActions();
        //$itemAction = $itemActions->firstWhere('name', $act);
        $itemAction = $this->itemAction($act);

        if (is_object($itemAction)) {
            return $itemAction->btn(['row' => $this->row]);
        }
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param Request                               $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery($data, $query) {
        //return $query->where('auth_user_id', $request->user()->auth_user_id);
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(Request $request, $query) {
        //return $query->where('auth_user_id', $request->user()->auth_user_id);
        //return $query->where('user_id', $request->user()->id);
    }

    public function applyJoin($query) {
        $model = $query->getModel();
        if (method_exists($model, 'scopeWithPost')) {
            $query = $query->withPost('a');
        }

        return $query;
    }

    public function applyFilter($query, $filters) {
        //https://github.com/spatie/laravel-query-builder
        $lang = app()->getLocale();
        $filters_fields = $this->filters();

        $filters_rules = collect($filters_fields)->filter(function ($item) {
            return isset($item->rules);
        })->map(function ($item) {
            return [$item->param_name => $item->rules];
        })->collapse()
            ->all();

        $validator = Validator::make($filters, $filters_rules);
        if ($validator->fails()) {
            \Session::flash('error', 'error');
            $id = $query->getModel()->getKeyName();

            return $query->whereNull($id); //restituisco query vuota
        }

        $filters_fields = collect($filters_fields)->filter(function ($item) use ($filters) {
            return in_array($item->param_name, array_keys($filters));
        })
            ->all();

        foreach ($filters_fields as $k => $v) {
            $filter_val = $filters[$v->param_name];
            if ('' != $filter_val) {
                if (! isset($v->op)) {
                    $v->op = '=';
                }
                if (isset($v->where_method)) {
                    $query = $query->{$v->where_method}($v->field_name, $filter_val);
                } else {
                    $query = $query->where($v->field_name, $v->op, $filter_val);
                }
            }
        }

        return $query;
    }

    public function applySearch($query, $q) {
        $tipo = 0; //0 a mano , 1 repository, 2 = scout

        switch ($tipo) {
            case 0:
                $search_fields = $this->search(); //campi di ricerca
                if (0 == count($search_fields)) { //se non gli passo nulla, cerco in tutti i fillable
                    $search_fields = with(new $this::$model())->getFillable();
                }
                $table = with(new $this::$model())->getTable();
                if (strlen($q) > 1) {
                    $query->where(function ($subquery) use ($search_fields, $q, $table) {
                        foreach ($search_fields as $k => $v) {
                            /*
                            if (! Str::contains($v, '.')) {
                                $v = $table.'.'.$v;
                            }
                            */
                            if (Str::contains($v, '.')) {
                                [$rel, $rel_field] = explode('.', $v);
                                //dddx([$rel, $rel_field, $q]);
                                $subquery->orWhereHas($rel, function ($subquery1) use ($rel_field, $q) {
                                    $subquery1->where($rel_field, 'like', '%'.$q.'%');
                                });
                            } else {
                                $subquery->orWhere($v, 'like', '%'.$q.'%');
                            }
                        }
                    });
                }

                return $query;
                break;
            case 1:
                $repo = with(new \Modules\Food\Repositories\RestaurantRepository())->search('grom');
                //ddd($repo->paginate());
                return $repo;
                break;
            case 2:

                break;
        } //end switch
    }

    //end applySearch

    public function applySort($query, $sort) {
        if (! is_array($sort)) {
            return $query;
        }
        $column = $sort['by'];
        /*
        * valutare se mettere controllo se colonna e' sortable
        **/
        if ('' == $column) {
            return $query;
        }
        $direction = isset($sort['order']) ? $sort['order'] : 'asc';
        $tmp = explode('|', $column);
        if (count($tmp) > 1) {
            $column = $tmp[0];
            $direction = $tmp[1];
        }
        $query = $query->orderBy($column, $direction);

        return $query;
    }

    public function indexRows(Request $request, $query) {
        $data = $request->all();

        $filters = $data;
        $q = isset($data['q']) ? $data['q'] : null;
        $out_format = isset($data['format']) ? $data['format'] : null;
        $act = isset($data['_act']) ? $data['_act'] : null;

        $query = $query->with($this->with());
        $query = $this->indexQuery($request, $query);

        //$query=$query->withPost('a');
        $query = $this->applyJoin($query);
        $query = $this->applyFilter($query, $filters);
        $query = $this->applySearch($query, $q);

        //$this->callAction($query, $act);

        $formatted = $this->formatData($query, $data);
        $page = isset($data['page']) ? $data['page'] : 1;
        Cache::forever('page', $page);
        //ddd(Cache::get('page'));
        //session('page',$page);
        //Cookie::make('page', $page, 20);
        //ddd(Cookie::get('page'));
        return $query;
    }

    public function formCreate($params = []) {
        return (new PanelFormService($this))->formCreate($params);
    }

    public function formEdit($params = []) {
        return (new PanelFormService($this))->formEdit($params);
    }

    public function exceptFields($params = []) {
        extract($params);
        $excepts = collect([]);
        if (is_object($this->rows)) {
            $methods = [
                'getForeignKeyName',
                'getMorphType',
                //'getLocalKeyName',
                'getForeignPivotKeyName',
                'getRelatedPivotKeyName',
                'getRelatedKeyName',
            ];
            if ('index' != $act) { //nella lista voglio visualizzare l'id
                $methods[] = 'getLocalKeyName';
            }

            foreach ($methods as $method) {
                if (method_exists($this->rows, $method)) {
                    $excepts = $excepts->merge($this->rows->$method());
                }
            }
        }
        $excepts = $excepts->unique()->all();

        $fields = collect($this->fields())
            ->filter(
                function ($item) use ($excepts, $act) {
                    if (! isset($item->except)) {
                        $item->except = [];
                    }

                    //!in_array($item->type,['Password']) &&
                    return ! in_array($act, $item->except) &&
                        ! in_array($item->name, $excepts);
                }
            )->all();

        return $fields;
    }

    public function indexFields() {
        $fields = $this->exceptFields(['act' => 'index']);

        return $fields;
    }

    public function createFields() {
        $fields = $this->exceptFields(['act' => 'create']);

        return $fields;
    }

    public function editFields() {
        $fields = $this->exceptFields(['act' => 'edit']);

        return $fields;
    }

    public function indexEditFields() {
        $fields = $this->exceptFields(['act' => 'index_edit']);

        return $fields;
    }

    /*
        -- in ingresso "qs" che e' array con le cose da aggiungere
    */
    public function addQuerystringsUrl($params) {
        extract($params);

        return $request->fullUrlWithQuery($qs); // fa il merge in automatico
        /*
        $currentQueries = $request->query();
        //Declare new queries you want to append to string:
        //$newQueries = ['year' => date('Y')];
        //Merge together current and new query strings:
        $allQueries = array_merge($currentQueries, $qs);
        //Generate the URL with all the queries:
        return $request->fullUrlWithQuery($allQueries);
        */
    }

    //------- navigazioni ---

    public function yearNavRedirect() {
        return NavService::yearNavRedirect();
    }

    public function yearNav() {
        return NavService::yearNav();
    }

    public function monthYearNav() { //possiamo trasformarlo in una macro
        return NavService::monthYearNav();
    }

    //-- nella registrazione 1 tasto, nelle modifiche 3
    public function btnSubmit($params = []) {
        return (new PanelFormService($this))->btnSubmit($params);
    }

    /*
     return (new PanelFormService($this))->formCreate($params);
    */
    public function btnDelete($params = []) {
        return (new PanelFormService($this))->btnDelete($params);
    }

    public function btnDetach($params = []) {
        return (new PanelFormService($this))->btnDetach($params);
    }

    public function btnCrud($params = []) {
        return (new PanelFormService($this))->btnCrud($params);
    }

    public function btnHtml($params) {
        return (new PanelFormService($this))->btnHtml($params);
    }

    public function btn($act, $params = []) {
        return (new PanelFormService($this))->btn($act, $params);
    }

    public function imageHtml($params) { //usare PanelImageService
        /*
        * mettere imageservice, o quello di spatie ?
        *
        **/
        $params['src'] = $this->row->image_src;
        $img = new ImageService($params);
        $src = $img->fit()->save()->src();

        return '<img src="'.asset($src).'" >';
    }

    public function imgSrc($params) { //usare PanelImageService
        $row = $this->row;
        $src = $row->image_src;

        $str0 = '/laravel-filemanager/';
        if (Str::startsWith($src, $str0)) {
            $src = '/'.Str::after($src, $str0);
        }
        $params['src'] = $src;
        extract($params);
        $images = $row->images;
        if (null == $images) {
            $images = $row->images();
        }
        $src = $src.'';
        $img = $images->where('src', $src)
            ->where('width', $width)
            ->where('height', $height)
            ->first();
        if (is_object($img)) {
            if (Str::startsWith($img->src_out, '\\')) {
                $img->src_out = Str::after($img->src_out, '\\');
                $img->save();
            }

            return $img->src_out;
        }
        $params['dirname'] = '/photos/'.$this->postType().'/'.$this->guid();

        try {
            $img = new ImageService($params);
            $src_out = $img->fit()->save()->src();

            $row->images()->create([
                'src' => $src,
                'src_out' => $src_out,
                'width' => $width,
                'height' => $height,
            ]);
            //ddd($src_out);
            return $src_out;
        } catch (\Exception $e) {
            //dddx('isolated');
            return '#['.__LINE__.']['.__FILE__.']';
        }
    }

    public function microdataSchemaOrg() {
        return '';
    }

    public function show_ldJson() {
        return [];
    }

    public function langUrl($lang) {
        $params = [];
        $params['lang'] = $lang;
        $params['panel'] = $this;

        return RouteService::urlLang($params);
    }

    public function relatedUrlRecursive($params) { //vedere chi lo usa
        $obj = $this;
        $items = [];
        $items[] = $this;
        while (isset($obj->parent)) {
            $items[] = $obj->parent;
            $obj = $obj->parent;
        }

        $count = count($items);
        $parz = [];
        $routename = [];
        for ($i = 0; $i < $count; ++$i) {
            $j = $count - $i - 1;
            $parz['container'.$i] = $items[$j]->postType();
            $parz['item'.$i] = $items[$j]->row;
            $routename[] = 'container'.$i;
        }
        $parz['container'.$count] = $params['related_name'];
        $parz['lang'] = app()->getLocale();
        $routename[] = 'container'.$i;
        $routename = implode('.', $routename).'.'.$params['act'];
        $route = route($routename, $parz);

        return $route;
    }

    public function relatedUrl($params) {
        /*
        $params['row'] = $this->row;
        return RouteService::urlRelated($params);
        */
        $params['panel'] = $this;

        return RouteService::urlRelatedPanel($params);
    }

    public function relatedName($name, $id = null) {
        //bell_boy => Modules\Food\Models\BellBoy
        $model = xotModel($name);
        if (null != $id) {
            $model = $model->find($id);
        }
        $panel = Panel::get($model);
        if (! is_object($panel)) {
            //dddx($model);
            return null;
        }
        $panel = $panel->setParent($this);

        return $panel;
    }

    public function url($params = []) {
        $act = 'show';
        extract($params);
        $act = Str::snake($act);

        return RouteService::urlPanel(['panel' => $this, 'act' => $act]);
    }

    public function indexUrl() {
        //$url = RouteService::urlModel(['model' => $this->row, 'panel_parent' => $this->parent, 'act' => 'index']);
        $url = RouteService::urlPanel(['panel' => $this, 'act' => 'index']);
        //--- da spostare in routeservice
        $data = [];
        $filters = $this->filters();

        foreach ($filters as $k => $v) {
            $field_value = $this->row->{$v->field_name};
            if (! isset($v->where_method)) {
                $v->where_method = 'where';
            }
            $where = Str::after($v->where_method, 'where');

            $filters[$k]->field_value = $field_value;
            switch ($where) {
                case 'Year':
                    $value = $field_value->year;
                    break;
                case 'ofYear':
                    $value = \Request::input('year', date('Y'));
                    break;
                case 'Month':
                    $value = $field_value->month;
                    break;
                default:
                    $value = $field_value;
                    break;
            }
            $filters[$k]->value = $value;
        }
        $queries = collect($filters)->pluck('value', 'param_name')->all();
        $node = class_basename($this->row).'-'.$this->row->getKey();
        $queries['page'] = Cache::get('page');

        $queries = array_merge(request()->query(), $queries);
        $queries = collect($queries)->except(['_act'])->all();
        $url = (url_queries($queries, $url)).'#'.$node;

        return $url;
    }

    public function indexEditUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'index_edit']);
    }

    public function editUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'edit']);
    }

    public function updateUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'update']);
    }

    public function showUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'show']);
    }

    public function createUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'create']);
    }

    public function storeUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'store']);
    }

    public function destroyUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'destroy']);
    }

    public function detachUrl() {
        return RouteService::urlPanel(['panel' => $this, 'act' => 'detach']);
    }

    public function gearUrl() {
        return '#';
    }

    /*
    public function postType(){
        $models=config('xra.model');
        $post_type=collect($models)->search(static::$model);
        if($post_type==''){
            $post_type=Str::snake(class_basename(static::$model));
        }
        return $post_type;
    }*/

    public function postType() {
        $post_type = collect(config('xra.model'))->search(get_class($this->row));
        if (false === $post_type) {
            $post_type = snake_case(class_basename($this->row));
        }

        return $post_type;
    }

    public function guid() {
        $row = $this->row;
        $key = $row->getRouteKeyName();
        $msg = [
            'key' => $key,
            '$row->getKey()' => $row->getKey(),
            '$row->getKeyName()' => $row->getKeyName(),
            '$row->primary_key' => $row->primaryKey,
            //'$row->$key' => $row->{$key},
            '$row->post' => $row->post,
            '$row' => $row,
        ];
        if (null == $row->getKey()) {
            return null;
        }
        try {
            $guid = $row->$key;
        } catch (\Exception $e) {
            $guid = '';
        }
        if ('' == $guid && method_exists($row, 'post') && $key = 'guid') {
            if ('' == $row->id && '' != $row->post_id) {
                $row->id = $row->post_id; //finche netson non riabilita migrazioni
            }
            try {
                return $row->post->guid;
            } catch (\Exception $e) {
                $title = $this->postType().' '.$this->row->getKey();

                $post = $row->post()->firstOrCreate(
                    [
                        'lang' => app()->getLocale(),
                    ],
                    [
                        'title' => $title,
                        'guid' => Str::slug($title),
                    ]
                );

                return $post->guid;
            }
        }

        return $guid;
    }

    /*
    public function getTitle() {
       $name = $this->getName();
       //$title = str_replace('_', ' ', $title);
       $row = $this->panel->row;

       $module_name_low = strtolower(getModuleNameFromModel($row));
       $title = trans($module_name_low.'::'.strtolower(class_basename($row)).'.act.'.$name);

       return $title;
    }
    */

    public function getItemTabs() {
        return (new PanelTabService($this))->getItemTabs();
    }

    public function getRowTabs() {
        return (new PanelTabService($this))->getRowTabs();
    }

    public function getTabs() {
        return (new PanelTabService($this))->getTabs();
    }

    public function rows($data = null) {
        if (null == $data) {
            $data = request()->all();
        }
        $filters = $data;
        $q = isset($data['q']) ? $data['q'] : null;
        $out_format = isset($data['format']) ? $data['format'] : null;
        $sort = isset($data['sort']) ? $data['sort'] : null;
        //$act = isset($data['_act']) ? $data['_act'] : null;

        $query = $this->rows;
        if (! is_object($query)) {
            if (! is_object($this->row)) {
                return $query;
            }
            $query = $this->row;
        }
        //ddd(get_class($this));
        $with = $this->with();
        if (! is_array($with)) {
            $msg = [
                'class' => get_class($this),
                'with' => $with,
            ];
            ddd($with);
        }
        $query = $query->with($with);
        $query = $this->indexQuery($data, $query);

        //$query=$query->withPost('a');
        $query = $this->applyJoin($query);
        $query = $this->applyFilter($query, $filters);
        $query = $this->applySearch($query, $q);
        $query = $this->applySort($query, $sort);
        $page = isset($data['page']) ? $data['page'] : 1;
        Cache::forever('page', $page);
        /*
        $this->callAction($query, $act);

        $formatted = $this->formatData($query, $data);
        $query=$query->paginate(20);
        return $query;
        */
        return $query;
    }

    public function callItemActionWithGate($act) {
        //$actions = $this->actions();
        //dddx([get_class($this), $actions]);

        return $this->callItemAction($act);
    }

    public function callAction($act) {
        //$act = Str::camel($act);

        $action = $this->getActions()
            ->firstWhere('name', $act);
        if (! is_object($action)) {
            abort(403, 'action '.$act.' not recognized for ['.get_class($this).']');
        }

        $action->setRow($this->row);
        $rows = $this->rows();
        $action->setRows($rows);

        $action->setPanel($this);

        $method = request()->getMethod();
        if ('GET' == $method) {
            return  $action->handle();
        } else {
            return $action->postHandle();
        }
    }

    public function callItemAction($act) {
        if (null == $act) {
            return null;
        }
        $action = $this->itemActions()
            ->firstWhere('name', $act);
        if (! is_object($action)) {
            return null;
        }
        $action->setRow($this->row);
        $action->setPanel($this);
        $method = request()->getMethod();
        if ('GET' == $method) {
            $out = $action->handle();
        } else {
            $out = $action->postHandle();
        }

        return $out;
    }

    public function callContainerAction($act) {
        if (null == $act) {
            return null;
        }
        $action = $this->containerActions()
            ->firstWhere('name', $act);
        if (! is_object($action)) {
            abort(403, 'action '.$act.' not recognized');
        }

        $data = request()->all();
        $rows = $this->rows($data);
        $action->setRows($rows);
        $action->setPanel($this);
        $method = request()->getMethod();
        if ('GET' == $method) {
            $out = $action->handle();
        } else {
            $out = $action->postHandle();
        }

        return $out;
    }

    public function out($params = []) {
        //return $this->view();
        return $this->presenter->out();
    }

    public function pdfFilename($params = []) {
        $fields = ['matr', 'cognome', 'nome', 'anno'];
        extract($params);
        $filename_arr = [];
        $filename_arr[] = $this->postType();
        $filename_arr[] = $this->guid();
        foreach ($fields as $field) {
            if (isset($this->row->$field)) {
                $filename_arr[] = $this->row->$field;
            }
        }
        $filename_arr[] = date('Ymd');
        $filename = implode('_', $filename_arr);
        if (request()->input('debug')) {
            $filename .= '.html';
        } else {
            $filename .= '.pdf';
        }

        return $filename;
    }

    public function pdf($params = []) {
        //return (new PdfPresenter($this))->out($params); //da fare

        if (! isset($params['view_params'])) {
            $params['view_params'] = [];
        }
        $view = ThemeService::getView(); //progressioni::admin.schede.show
        $view .= '.pdf';
        $view = str_replace('.store.', '.show.', $view);
        extract($params);

        $html = view($view)
            ->with('view', $view)
            ->with('row', $this->row)
            ->with('rows', $this->rows)
            ->with($params['view_params']);

        //ddd($this->rows->get());
        if (request()->input('debug')) {
            return $html;
        }
        $params['html'] = (string) $html;

        return HtmlService::toPdf($params);
    }

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function related($relationship) {
        $related = $this->row->$relationship()->getRelated();
        $panel_related = Panel::get($related);
        $panel_related->setParent($this);

        return $panel_related;
    }

    public function getModuleName() {
        $model = $this::$model;
        $module_name = Str::before(Str::after($model, 'Modules\\'), '\\Models\\');

        return $module_name;
    }

    public function getModuleNameLow() {
        return Str::lower($this->getModuleName());
    }

    public function breadcrumbs() {
        $curr = $this;
        $parents = [];
        while (null != $curr) {
            $parents[] = $curr;
            $curr = $curr->getParent();
        }
        $bread = [];
        $tmp = (object) [];
        $tmp->url = asset(app()->getLocale());
        $tmp->title = 'Home';
        $tmp->obj = \Theme::xotModel('home');
        $tmp->method = 'index';
        $bread[] = $tmp;
        foreach ($parents as $parent) {
            $tmp = (object) [];
            $tmp->url = $parent->indexUrl();
            $tmp->title = $parent->postType();
            $tmp->obj = \Theme::xotModel($tmp->title);
            $tmp->method = 'index';
            $bread[] = $tmp;
            try {
                $tmp = (object) [];
                $tmp->url = $parent->showUrl();
                $tmp->title = $parent->row->title;
                $tmp->obj = \Theme::xotModel($parent->postType());
                $tmp->method = 'show';
                $bread[] = $tmp;
            } catch (\exception $e) {
            }
        }
        //dddx($bread);

        return $bread;
    }

    public function getExcerpt($length = 225) {
        $row = $this->row;
        //$content = $row->subtitle ?? $row->txt;

        if ($row->subtitle) {
            $content = $row->subtitle;
        } else {
            $content = $row->txt;
        }

        $cleaned = strip_tags(
            preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content),
            '<code>'
        );
        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated).'...'
            : $cleaned;
    }

    public function indexEditSubs() {
        return [];
    }

    public function swiperItem() {
        return 'pub_theme::layouts.swiper.item';
    }

    public function view($params = null) {
        return $this->presenter->out($params);
    }

    public function id() {
        $curr = $this;
        $data = collect([]);
        while (null != $curr) {
            $data->prepend($curr->postType().'-'.$curr->guid());
            $curr = $curr->getParent();
        }

        return $data->implode('-');
    }
}

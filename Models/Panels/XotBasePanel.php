<?php

namespace Modules\Xot\Models\Panels;

use Collective\Html\FormFacade as Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
//----------  SERVICES --------------------------
use Modules\Xot\Services\ImageService;
use Modules\Xot\Services\RouteService;
use Modules\Xot\Services\StubService;
use Modules\Xot\Services\PanelService as Panel;


abstract class XotBasePanel {
    public $out = null;
    public $force_exit = false;
    public $msg = 'msg from panel';
    public $row = null;
    public $rows = null;

    public function __construct($model = null) {
        $this->row = $model;
    }

    public function setRow($row) {
        $this->row = $row;
    }

    public function setRows($rows) {
        $this->rows = $rows;
    }

    public function optionIdName() {
        return 'id';
    }

    public function optionLabelName() {
        return 'matr';
    }

    public function search() {
        return [];
    }

    public function rules($params=[]) {
        $act='';
        extract($params);
        switch($act){
            case 'store':$fields = $this->createFields();break;
            case 'update':$fields = $this->editFields();break;
            default:$fields = $this->fields();break;
        }

        
        $rules = collect($fields)->map(function ($item) {
            if (! isset($item->rules)) {
                $item->rules = '';
            }
            if ('pivot_rules' == $item->rules) {
                $rel_name = $item->name;
                $pivot_class = with(new $this::$model())->$rel_name()->getPivotClass();
                $pivot = new $pivot_class();
                $pivot_panel = StubService::getByModel($pivot, 'panel', $create = true);
                //ddd('preso ['.$pivot_class.']['.get_class($pivot_panel).']');
                $pivot_rules = collect($pivot_panel->rules())->map(function ($pivot_rule_val, $pivot_rule_key) use ($item) {
                    return [$item->name.'.*.pivot.'.$pivot_rule_key => $pivot_rule_val];
                })->collapse()->all();

                return $pivot_rules;
            }

            return [$item->name => $item->rules];
        })->collapse()->all();
        return $rules;
    }

    public function pivotRules($params) {
        extract($params);
    }

    public function rulesMessages() {
        $lang = \App::getLocale();
        $rules_msg_fields = collect($this->fields())->filter(function ($value, $key) use ($lang) {
            return isset($value->rules_messages) && isset($value->rules_messages[$lang]);
        })
        ->map(function ($item) use ($lang) {
            $tmp = [];
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

    public function getXotModelName() {
        return collect(config('xra.model'))->search(static::$model);
    }

    public function indexNav() {
        return null;
    }

    public function containerActions() {
        $actions = collect($this->actions())->filter(function ($item) {
            return isset($item->onContainer) && $item->onContainer;
        })->all();

        return $actions;
    }

    public function itemActions() {
        $actions = collect($this->actions())->filter(function ($item) {
            return isset($item->onItem) && $item->onItem;
        })->all();

        return $actions;
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param Request                               $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(Request $request, $query) {
        //return $query->where('auth_user_id', $request->user()->auth_user_id);
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param Request                               $request
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
        $lang = \App::getLocale();
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
                   $query->where(function ($subquery) use ($search_fields,$q,$table) {
                       foreach ($search_fields as $k => $v) {
                           if (! Str::contains($v, '.')) {
                               $v = $table.'.'.$v;
                           }
                           $subquery->orWhere($v, 'like', '%'.$q.'%');
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
        }//end switch
    }

    //end applySearch

    //-- da studiare --
    protected static function applySearchNova($query, $search) {
        return $query->where(function ($query) use ($search) {
            if (is_numeric($search) && in_array($query->getModel()->getKeyType(), ['int', 'integer'])) {
                $query->orWhere($query->getModel()->getQualifiedKeyName(), $search);
            }

            $model = $query->getModel();

            foreach (static::searchableColumns() as $column) {
                if (is_array($column)) {
                    foreach ($column as $key => $col) {
                        $column[$key] = $model->qualifyColumn($col);
                    }
                    $concat = implode(", ' ', ", $column);
                    $query->orWhereRaw('CONCAT('.$concat.") LIKE '%".$search."%'");
                } else {
                    $query->orWhere($model->qualifyColumn($column), 'like', '%'.$search.'%');
                }
            }
        });
    }

    public function formatData($data, $params) {
        extract($params);
        if (! isset($format)) {
            return $data;
        }
        if ('json' == $format) { //potrei ficcare anche xls
            //ddd($this->option_id());
            //$res=$data->pluck('dest1','repar')->all();

            //die(json_encode($res));
            $ris = $data->paginate(20);
            $this->force_exit = 1;
            //$this->out=json_encode($res);
            //$this->out=new \Modules\Progressioni\Transformers\ProgressioniCollection($ris);
            $this->out = $ris->toJson();
            //$this->out=\Modules\Progressioni\Transformers\ProgressioniResource::collection($ris);
            return;
        }
        if ('typeahead' == $format) {
            /*
            $ris=$data->select('area_id as id','area_define_name as label')
                        ->paginate(10);
            */
            $ris = $data->paginate(20);
            $this->force_exit = 1;
            $model_class = $this::$model;
            //ddd($model_class);//Modules\LU\Models\Area
            $model_class_name = class_basename($model_class);
            $module_ns = Str::before($model_class, '\\Models\\');
            $transformers_coll = $module_ns.'\\Transformers\\'.$model_class_name.'Collection';
            $transformers_res = $module_ns.'\\Transformers\\'.$model_class_name.'Resource';
            //Typeahead
            //ddd($transformers_coll);
            //$this->out=new $transformers_coll($ris);
            $this->out = $transformers_res::collection($ris); 

            return;
        }
        if($format=='geoJson' ){
            $this->force_exit = 1;
            /**
            * https://github.com/renelikestacos/Web-Mapping-Leaflet-NodeJS-Tutorials
            * https://github.com/shramov/leaflet-plugins/blob/master/examples/permalink.html
            *
            **/
            $cache_key='geoJson';
            $out=Cache::store('file')->get($cache_key, function () use($data){
                $lang=\App::getLocale();
                $ris=$data->select('post.post_id','post_type','guid','latitude','longitude')
                            ->where('latitude','!=','')
                            ->where('lang',$lang)
                            ->get();
                $out=new \Modules\Geo\Transformers\GeoJsonCollection($ris);
                return $out;
            });
            $this->out = $out;
            
        }


        return $data;
    }

    public function callAction($query, $act) {
        if (null == $act) {
            return $query;
        }
        $action = collect($this->actions())
            ->firstWhere('name', $act);
        $action->setRows($query);
        $this->out = $action->handle();
        if (null != $this->out) { //se c'e' un risultato
            $this->force_exit = true;
        }
    }

    //end callAction

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

        $this->callAction($query, $act);

        $formatted = $this->formatData($query, $data);

        return $query;
    }

    public function indexFields() {
        $fields = collect($this->fields())->filter(function ($item) {
            if (! isset($item->except)) {
                $item->except = [];
            }

            return ! in_array($item->type, ['Password']) &&
                ! in_array('index', $item->except);
        })->all();

        return $fields;
    }

    public function createFields() {
        $excepts = [];
        if (is_object($this->rows)) {
            if (method_exists($this->rows, 'getForeignKeyName')) {
                $excepts[] = $this->rows->getForeignKeyName();
            }
        }
        //ddd($excepts);
        $fields = collect($this->fields())->filter(function ($item) use ($excepts) {
            if (! isset($item->except)) {
                $item->except = [];
            }

                //!in_array($item->type,['Password']) &&
            return ! in_array('create', $item->except) &&
                ! in_array($item->name, $excepts)
                ;
        })->all();

        return $fields;
    }

    public function editFields() {
        $excepts = [];
        $excepts[] = 'id'; //??
        if (is_object($this->rows)) {
            $getters = ['getForeignKeyName', 'getMorphType', 'getForeignPivotKeyName', 'getRelatedPivotKeyName', 'getRelatedKeyName'];
            foreach ($getters as $k => $v) {
                if (method_exists($this->rows, $v)) {
                    $excepts[] = $this->rows->$v();
                }
            }
        }
        //ddd($excepts);
        $fields = collect($this->fields())->filter(function ($item) use ($excepts) {
            if (! isset($item->except)) {
                $item->except = [];
            }

            return
                //!in_array($item->type,['Password']) &&
                ! in_array('edit', $item->except) &&
                ! in_array($item->name, $excepts)
                ;
        })->all();

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

    public function yearNav() {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $params = \Route::current()->parameters();
        $redirect = 1;
        if ('' == $request->year) {
            if ($redirect) {
                $t = $this->addQuerystringsUrl(['request' => $request, 'qs' => ['year' => date('Y')]]);
                $this->force_exit = true;
                $this->out = redirect($t);
                die($this->out); //forzatura
                return;
            }
            $request->year = date('Y');
        }
        $year = $request->year - 1;
        $nav = [];
        for ($i = 0; $i < 3; ++$i) {
            $tmp = [];
            $params['year'] = $year;
            $tmp['title'] = $year;
            if (date('Y') == $params['year']) {
                $tmp['title'] = '['.$tmp['title'].']';
            }
            if ($request->year == $params['year']) {
                $tmp['active'] = 1;
            } else {
                $tmp['active'] = 0;
            }
            $tmp['url'] = route($routename, $params);
            $nav[] = (object) $tmp;
            ++$year;
        }

        return $nav;
    }

    public function monthYearNav() { //possiamo trasformarlo in una macro
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $params = \Route::current()->parameters();
        if ('' == $request->year) {
            $request->year = date('Y');
        }
        if ('' == $request->month) {
            $request->month = date('m');
        }
        $q = 2;
        $d = Carbon::create($request->year, $request->month, 1)->subMonth($q);
        $nav = [];
        for ($i = 0; $i < ($q * 2) + 1; ++$i) {
            $tmp = [];
            $params['month'] = $d->format('m') * 1;
            $params['year'] = $d->format('Y') * 1;
            $tmp['title'] = $d->isoFormat('MMMM YYYY');
            if (date('Y') == $params['year'] && date('m') == $params['month']) {
                $tmp['title'] = '['.$tmp['title'].']';
            }
            if ($request->year == $params['year'] && $request->month == $params['month']) {
                $tmp['active'] = 1;
            } else {
                $tmp['active'] = 0;
            }
            $tmp['url'] = route($routename, $params);
            $nav[] = (object) $tmp;
            $d->addMonth();
        }

        return $nav;
        //$d->locale() //it !!
        /*
        return '';
        */
    }

    //-- nella registrazione 1 tasto, nelle modifiche 3
    public function btnSubmit() {
        return Form::bsSubmit('save');
    }

    public function imageHtml($params) {
        /*
        * mettere imageservice, o quello di spatie ?
        *
        **/
        $params['src'] = $this->row->image_src;
        $img = new ImageService($params);
        $src = $img->fit()->save()->src();

        return '<img src="'.asset($src).'" >';
    }

    public function microdataSchemaOrg() {
        return '';
    }

    public function show_ldJson() {
        return [];
    }

    public function url() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'show']);
    }

    public function indexUrl() {
        $url=RouteService::urlModel(['model' => $this->row, 'act' => 'index']);
        $data=[];
        $filters=$this->filters();
        foreach($filters as $k => $v){
            $field_value=$this->row->{$v->field_name};
            if(!isset($v->where_method)) $v->where_method='where';
            $where=Str::after($v->where_method,'where');

            $filters[$k]->field_value=$field_value;
            switch($where){
                case 'Year': $value=$field_value->year;break;
                case 'Month': $value=$field_value->month;break;
                default: $value=$field_value;break;
            }
            $filters[$k]->value=$value;
        }
        $queries=collect($filters)->pluck('value','param_name')->all();
        $url=(url_queries($queries,$url));
        return $url;
                
    }

    public function editUrl() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'edit']);
    }

    public function updateUrl() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'update']);
    }

    public function showUrl() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'show']);
    }

    public function createUrl() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'create']);
    }

    public function storeUrl() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'store']);
    }

    public function destroyUrl() {
        return RouteService::urlModel(['model' => $this->row, 'act' => 'destroy']);
    }

    public function postType(){
        $models=config('xra.model');
        $post_type=collect($models)->search(static::$model);
        if($post_type==''){
            $post_type=Str::snake(class_basename(static::$model));
        }
        return $post_type;
    }

    public function getTabs() {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $act=last(explode('.',$routename));
        //$routename = \Route::current()->getName();
        $route_params = \Route::current()->parameters();
        [$containers,$items]=params2ContainerItem($route_params);
        $data=[];
        foreach($items as $k=>$item){
            $panel=Panel::get($item);
            $tabs=$panel->tabs();
            $row=[];
            if($k==0){
                if(Gate::allows('index', $this->row)){
                    $tmp=new \stdClass();
                    $tmp->title='<< Back';
                    $tmp->url=$panel->indexUrl();;
                    $tmp->active=false;
                    $row[]=$tmp;
                }
                //-----------------------
                $tmp=new \stdClass();
                if(in_array($act,['index_edit','edit','update'])){
                    $url=$panel->editUrl();break;
                }else{
                    $url=$panel->showUrl();break;
                }
                
                $tmp->url=$url;
                $tmp->title='Content ';//.'['.request()->url().']['.$url.']';
                if($url_test=1){
                    $tmp->active=request()->url()==$url;
                }else{
                    $tmp->active=request()->routeIs('admin.container0.'.$act);  
                }
                $row[]=$tmp;
                //----------------------
            }
            foreach($tabs as $tab){
                $tmp=new \stdClass();
                $tmp->title=$tab;

                if(in_array($act,['index_edit','edit','update'])){
                    $tab_act='index_edit';
                }else{
                    $tab_act='index';
                }
                $tmp->url=RouteService::urlRelated(['row'=>$item,'related_name'=>$tab,'act'=>$tab_act]);
                $tmp->active=in_array($tab,$containers);
                $row[]=$tmp;
            }
            $data[]=$row;
        }
        return $data;
    }
}

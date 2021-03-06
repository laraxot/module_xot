<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
//----------  SERVICES --------------------------
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Contracts\ModelContract;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Presenters\PdfPanelPresenter;
use Modules\Xot\Presenters\XlsPanelPresenter;
use Modules\Xot\Services\ChainService;
use Modules\Xot\Services\HtmlService;
use Modules\Xot\Services\ImageService;
use Modules\Xot\Services\PanelActionService;
use Modules\Xot\Services\PanelFormService;
use Modules\Xot\Services\PanelRouteService;
use Modules\Xot\Services\PanelService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\PanelTabService;
use Modules\Xot\Services\PolicyService;
use Modules\Xot\Services\RouteService;
use Modules\Xot\Services\StubService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class XotBasePanel.
 *
 * Modules\Xot\Models\Panels\XotBasePanel.
 */
abstract class XotBasePanel implements PanelContract {
    //public $out = null; //deprecated

    //public bool $force_exit = false; //deprecated

    //public string $msg = 'msg from panel';
    protected static string $model;

    //var \Illuminate\Contracts\Foundation\Application|mixed|null

    /**
     * @var Model|ModelContract|object
     */
    public $row = null;

    //var \Illuminate\Database\Eloquent\Collection|Builder|ModelContract[]

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public $rows = null;

    public ?string $name = null;

    public ?PanelContract $parent = null;

    public ?bool $in_admin = null;

    public array $route_params = [];

    public PanelPresenterContract $presenter;

    public PanelFormService $form;

    public PanelRouteService $route;

    public function __construct(PanelPresenterContract $presenter, PanelRouteService $route) {
        //$this->row = $model;
        //$this->presenter = $presenter;
        //$this->presenter->setPanel($this);
        $this->presenter = $presenter->setPanel($this);
        //$this->presenter->setPanel($this);
        $this->row = app($this::$model);
        $this->form = app(PanelFormService::class)->setPanel($this);
        $this->route = $route->setPanel($this);
        //$this->name = 'TEST';
    }

    /*
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    */

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public function with(): array {
        return [];
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getName(): string {
        if (null != $this->name) {
            return $this->name;
        } else {
            return $this->postType();
        }
    }

    /**
     * @param object $row
     */
    public function setRow($row): self {
        $this->row = $row;

        return $this;
    }

    /**
     * @param object|Model $rows
     */
    public function setRows($rows): self {
        $this->rows = $rows;

        return $this;
    }

    /**
     * get Rows.
     *
     * @return object|Model
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * @return $this
     */
    public function initRows(): self {
        $this->rows = $this->rows();
        //$this->rows = $this->rows; ??

        return $this;
    }

    //Parameter #1 $panel of method Modules\Xot\Contracts\PanelContract::setParent()
    //expects Modules\Xot\Contracts\PanelContract,
    //        Modules\Xot\Contracts\PanelContract|null given.
    public function setParent(?PanelContract $parent): self {
        $this->parent = $parent;

        return $this;
    }

    public function setBrother(?PanelContract $panel): self {
        $this->setParent($panel->getParent());
        $this->setName($panel->getName());

        return $this;
    }

    // se uso in rows() getQuery il dato ottenuto e' una collezione di items non di modelli
    public function getHydrate(object $data): PanelContract {
        if ('stdClass' == get_class($data)) {
            //$row = $this->row->hydrate((array) $data);
            $row = $this->row->forceFill((array) $data);
        } else {
            $row = $data;
        }
        $panel = PanelService::get($row);
        $panel->setParent($this->getParent());

        return $panel;
    }

    public function getParent(): ?PanelContract {
        return $this->parent;
    }

    public function getParents(): \Illuminate\Support\Collection {
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
        $panel_curr = $this->getParent();

        //while (null != $panel_curr->getParent()) {
        while (null != $panel_curr) {
            $parents->prepend($panel_curr);
            $panel_curr = $panel_curr->getParent();
        }

        return $parents;
    }

    public function getBreads(): \Illuminate\Support\Collection {
        $breads = $this->getParents();
        $breads->add($this);

        return $breads;
    }

    // questa funzione rilascia un array dei guid dell'url attuale
    public function getParentsGuid(): array {
        $parents = $this->getParents();
        $parent_first = $parents->first();
        if (is_object($parent_first)) {
            while (null != $parent_first->row->parent) {
                $parent_first = Panel::get($parent_first->row->parent);
                $parents->prepend($parent_first);
            }
        }
        $parents_guid = $parents
            ->map(function ($item) {
                return $item->row->guid;
            })
            ->all();

        return $parents_guid;
    }

    /**
     * @return mixed
     */
    public function findParentType(string $type) {
        return collect($this->getParents())->filter(
            function ($item) use ($type) {
                return $type == $item->postType();
            }
        )->first();
    }

    /**
     * @return mixed
     */
    public function optionId(object $row) {
        return $row->getKey();
    }

    /*
     * @return void
     */
    public function setInAdmin(bool $in_admin): void {
        $this->in_admin = $in_admin;
    }

    /*
     * @return bool|null
     */
    public function getInAdmin() {
        return $this->in_admin;
    }

    /*
     * @return void
     */
    public function setRouteParams(array $route_params): void {
        $this->route_params = $route_params;
    }

    public function getRouteParams(): array {
        $route_current = Route::current();
        $route_params = is_object($route_current) ? $route_current->parameters() : [];

        $route_params = array_merge($route_params, $this->route_params);

        return $route_params;
    }

    public function setItem(string $guid): self {
        $model = $this->row;
        $rows = $this->rows;
        $pk = $model->getRouteKeyName($this->in_admin);
        $pk_full = $model->getTable().'.'.$pk;

        if ('guid' == $pk) {
            $pk_full = 'guid';
        } // pezza momentanea

        $value = Str::slug($guid); //retrocompatibilita'
        if ('guid' == $pk_full) {
            $rows = $rows->whereHas(
                'posts',
                function (Builder $query) use ($value): void {
                    $query->where('guid', $value);
                }
            );
        } else {
            $rows = $rows->where([$pk_full => $value]);
        }

        if ($rows->count() > 1) {
            $row_last = $rows->latest()->first();
            if ('guid' == $pk_full) {
                $guid_old = $row_last->post->guid;
                $row_last->post->update(['guid' => $guid_old.'-1']);
            }
            //*
            dddx(
                [
                    'error' => 'multiple',
                    'rows' => $rows->get(),
                    'row_last' => $row_last,
                ]
            );
            //*/
        }
        $this->row = $rows->first();

        return $this;
    }

    //funzione/flag da settare a true ad ogni pannello/modello che abbia le traduzioni

    /**
     * @return false
     */
    public function hasLang() {
        return false;
    }

    public function setLabel(string $label): ModelContract {
        $model = $this->row;
        $res = $model::whereHas(
            'post',
            function (Builder $query) use ($label): void {
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
    public function optionLabel(object $row): string {
        return $row->matr.' ['.$row->email.']['.$row->ha_diritto.'] '.$row->cognome.' '.$row->cognome.' ';
    }

    public function title() {
        return optional($this->row)->title;
    }

    /**
     * @return array
     */
    public function optionsSelect() {
        $opts = [];
        $rows = $this->rows;
        if (null == $rows) {
            //dddx($this);
            $rows = $this->options();
        }

        foreach ($rows as $row) {
            $id = $this->optionId($row);
            $label = $this->optionLabel($row);

            $opts[$id] = $label;
        }

        return $opts;
    }

    /**
     * @param array|null $data
     *
     * @return mixed
     */
    public function options($data = null) {
        if (null == $data) {
            $data = request()->all();
        }
        //dddx($this->rows()->get());

        return $this->rows($data)->get();
    }

    public function optionsTree(array $data = []): array {
        if (null == $data /*|| empty($data)*/) {
            $data = request()->all();
        }
        $rows = $this->rows($data)->get();
        $primary_field = $this->row->getKeyName();
        $c = new ChainService($primary_field, 'parent_id', 'pos', $rows);

        $options = collect($c->chain_table)->map(
            function ($item) {
                //Parameter #2 $multiplier of function str_repeat expects int, float|int given.
                $label = str_repeat('------', (int) $item->indent + 1).$this->optionLabel($item);

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

    /**
     * @return mixed
     */
    public function optionIdName() {
        return $this->row->getKeyName();
    }

    /**
     * @return string
     */
    public function optionLabelName() {
        return 'matr';
    }

    /**
     * inserisco i campi dove si applicherà la funzione di ricerca testuale (solo nel pannello admin?)
     * se il pannello interessato rilascia un array vuoto, fa la ricerca tra i fillable del modello
     * esempio post.title, post.subtitle.
     */
    public function search(): array {
        return [];
    }

    /**
     * @return array
     */
    public function orderBy() {
        return [];
    }

    /**
     * @return mixed
     */
    public function getOrderField() {
        return $this->row->getKeyName();
    }

    /**
     * Get the actions available for the resource.
     */
    public function fields(): array {
        return [];
    }

    public function rules(array $params = []): array {
        $act = '';
        extract($params);
        if ('' == $act) {
            $route_action = (string) \Route::currentRouteAction();
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

        foreach ($fields as $field) {
            if (in_array($field->type, ['Cell', 'CellLabel'])) {
                foreach ($field->fields as $sub_field) {
                    $fields[] = $sub_field;
                }
            }
        }

        $rules = collect($fields)->map(
            function ($item) {
                if (! isset($item->rules)) {
                    $item->rules = '';
                }
                if (Str::contains($item->type, 'RelationshipOne')) {
                    $rows_tmp = $this->row->{$item->name}();
                    if (method_exists($rows_tmp, 'getLocalKeyName')) {
                        $name1 = $rows_tmp->getLocalKeyName();
                    } else {
                        $name1 = $rows_tmp->getForeignKeyName();
                    }
                    /*
                    dddx([
                        'msg' => 'preso',
                        'row' => $this->row,
                        'test' => $name1,
                    ]);
                    */
                    $item->name = $name1;
                }
                if ('pivot_rules' == $item->rules) {
                    $rel_name = $item->name;
                    $pivot_class = with(new $this::$model())
                        ->$rel_name()
                        ->getPivotClass();
                    $pivot = new $pivot_class();
                    $pivot_panel = StubService::getByModel($pivot, 'panel', true);
                    //dddx('preso ['.$pivot_class.']['.get_class($pivot_panel).']');
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

    public function pivotRules(array $params = []): array {
        extract($params);

        return [];
    }

    /**
     * @return array
     */
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
        //dddx($trans_ns);//food::restaurant_owner__rules_messages
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
     * @return array
     */
    public function filters(Request $request = null) {
        return [];
    }

    /**
     * @return false|mixed
     */
    public function getXotModelName() {
        return collect(config('xra.model'))->search(static::$model);
    }

    /**
     * @return null
     */
    public function indexNav() {
        return null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getActions(array $params = []) {
        return (new PanelActionService($this))->getActions($params);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function containerActions(array $params = []) {
        return (new PanelActionService($this))->containerActions($params);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function itemActions(array $params = []) {
        return (new PanelActionService($this))->itemActions($params);
    }

    /**
     * @param string $act
     *
     * @return mixed
     */
    public function itemAction($act) {
        return (new PanelActionService($this))->itemAction($act);
    }

    /**
     * @param string $act
     *
     * @return mixed
     */
    public function containerAction($act) {
        return (new PanelActionService($this))->containerAction($act);
    }

    /**
     * @param string $act
     *
     * @return mixed
     */
    public function urlContainerAction($act) {
        return (new PanelActionService($this))->urlContainerAction($act);
    }

    /**
     * @param string $act
     *
     * @return mixed
     */
    public function urlItemAction($act) {
        return (new PanelActionService($this))->urlItemAction($act);
    }

    /**
     * @param string $act
     *
     * @return mixed
     */
    public function btnItemAction($act) {
        return (new PanelActionService($this))->btnItemAction($act);
    }

    //-- nella registrazione 1 tasto, nelle modifiche 3
    //public function btnSubmit() {
    //return Form::bsSubmit('save');
    //    return Form::bsSubmit(trans('xot::buttons.save'));
    //}

    /**
     * Build an "index" query for the given resource.
     *
     * @param Builder|HasMany $query
     *
     * @return Builder|HasMany
     */
    public static function indexQuery(array $data, $query) {
        //return $query->where('auth_user_id', $request->user()->auth_user_id);
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     */
    public static function relatableQuery(Request $request, Builder $query): Builder {
        //return $query->where('auth_user_id', $request->user()->auth_user_id);
        //return $query->where('user_id', $request->user()->id);
        return $query;
    }

    /* da rivalutare
    public function applyJoin(Builder $query): Builder {
        $model = $query->getModel();
        if (method_exists($model, 'scopeWithPost')) {
            $query = $query->withPost('a');
        }

        return $query;
    }
    */

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Relations\HasMany $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function applyFilter($query, array $filters) {
        //https://github.com/spatie/laravel-query-builder
        $lang = app()->getLocale();
        $filters_fields = $this->filters();

        $filters_rules = collect($filters_fields)
            ->filter(
                function ($item) {
                    return isset($item->rules);
                }
            )->map(
                function ($item) {
                    return [$item->param_name => $item->rules];
                }
            )->collapse()
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
                    if (! isset($v->field_name)) {
                        dddx(['err' => 'field_name is missing']);

                        return $query;
                    }
                    $query = $query->{$v->where_method}($v->field_name, $filter_val);
                } else {
                    $query = $query->where($v->field_name, $v->op, $filter_val);
                }
            }
        }

        return $query;
    }

    /**
     * https://lyften.com/projects/laravel-repository/doc/searching.html.
     * https://spatie.be/docs/laravel-query-builder/v3/features/filtering
     * https://github.com/spatie/laravel-query-builder/issues/452.
     * https://forum.laravel-livewire.com/t/anybody-using-spatie-laravel-query-builder-with-livewire/299/5
     * https://github.com/spatie/laravel-query-builder/issues/243.
     * https://github.com/spatie/laravel-query-builder/pull/223.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function applySearch($query, ?string $q) {
        if (! isset($q)) {
            return $query;
        }
        /*
        $test = QueryBuilder::for($query)
            //->allowedFilters(Filter::FiltersExact('q', 'matr'))
            ->allowedFilters([AllowedFilter::exact('q', 'matr'), AllowedFilter::exact('q', 'ente')])
            // ->allowedIncludes('posts')
            //->allowedFilters('matr')
            ->get();
        */
        /*
            ->allowedFilters([
                Filter::search('q', ['first_name', 'last_name', 'address.city', 'address.country']),
            ]);
        */
        //dddx($test);

        $tipo = 0; //0 a mano , 1 repository, 2 = scout
        switch ($tipo) {
            case 0:
                $search_fields = $this->search(); //campi di ricerca
                if (0 == count($search_fields)) { //se non gli passo nulla, cerco in tutti i fillable
                    $search_fields = with(new $this::$model())->getFillable();
                }
                $table = with(new $this::$model())->getTable();
                if (strlen($q) > 1) {
                    $query = $query->where(function ($subquery) use ($search_fields, $q): void {
                        foreach ($search_fields as $k => $v) {
                            if (Str::contains($v, '.')) {
                                [$rel, $rel_field] = explode('.', $v);
                                $subquery = $subquery->orWhereHas(
                                    $rel,
                                    function (Builder $subquery1) use ($rel_field, $q): void {
                                        $subquery1->where($rel_field, 'like', '%'.$q.'%');
                                    }
                                );
                            } else {
                                $subquery = $subquery->orWhere($v, 'like', '%'.$q.'%');
                            }
                        }
                    });
                }
                //dddx(['q' => $q, 'sql' => $query->toSql()]);

                return $query;
               // break;
            case 1:
                //$repo = with(new \Modules\Food\Repositories\RestaurantRepository())->search('grom');
                //dddx($repo->paginate());
                //return $repo;
                break;
            case 2:
                break;
        } //end switch

        return $query;
    }

    //end applySearch

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function applySort($query, ?array $sort) {
        if (! is_array($sort)) {
            return $query;
        }
        //dddx([$query, $sort]);
        if (isset($sort['by'])) {
            $column = $sort['by'];
        } else {
            $column = '';
        }
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

    //Method Modules\Xot\Models\Panels\XotBasePanel::indexRows() has parameter $query with no typehint specified.
    /* deprecated ??
    public function indexRows(Request $request, $query): Builder {
        dddx([get_class($query)]);
        $data = $request->all();

        $filters = $data;
        $q = isset($data['q']) ? $data['q'] : null;
        $out_format = isset($data['format']) ? $data['format'] : null;
        $act = isset($data['_act']) ? $data['_act'] : null;

        $query = $query->with($this->with());
        //$query = $this->indexQuery($request, $query);
        $query = $this->indexQuery($data, $query);

        //$query=$query->withPost('a');
        //$query = $this->applyJoin($query);
        $query = $this->applyFilter($query, $filters);
        $query = $this->applySearch($query, $q);

        //$this->callAction($query, $act);

        //$formatted = $this->formatData($query, $data); // to presenter
        $page = isset($data['page']) ? $data['page'] : 1;
        Cache::forever('page', $page);
        //dddx(Cache::get('page'));
        //session('page',$page);
        //Cookie::make('page', $page, 20);
        //dddx(Cookie::get('page'));
        return $query;
    }
    */

    /**
     * @return mixed
     */
    public function formCreate(array $params = []) {
        return $this->form->formCreate($params);
    }

    /**
     * @return mixed
     */
    public function formEdit(array $params = []) {
        return $this->form->formEdit($params);
    }

    public function formLivewireEdit(array $params = []) {
        return $this->form->formLivewireEdit($params);
    }

    public function getFormData(array $params = []) {
        return $this->form->{__FUNCTION__}($params);
    }

    /* -- to panelformservice
    public function exceptFields(array $params = []) {
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
    */

    /**
     * @return mixed
     */
    public function indexFields() {
        //return $this->exceptFields(['act' => 'index']);
        return $this->form->{__FUNCTION__}();
    }

    /**
     * @return mixed
     */
    public function createFields() {
        //return  $this->exceptFields(['act' => 'create']);

        return $this->form->{__FUNCTION__}();
    }

    /**
     * @return mixed
     */
    public function editFields() {
        //return  $this->exceptFields(['act' => 'edit']);

        return $this->form->{__FUNCTION__}();
    }

    /**
     * @return mixed
     */
    public function editObjFields() {
        return $this->form->{__FUNCTION__}();
    }

    /**
     * @return mixed
     */
    public function indexEditFields() {
        //return  $this->exceptFields(['act' => 'index_edit']);

        return $this->form->{__FUNCTION__}();
    }

    /*
        -- in ingresso "qs" che e' array con le cose da aggiungere
    */
    /* --- da mettere in routeService
    public function addQuerystringsUrl($params) {
        extract($params);

        return $request->fullUrlWithQuery($qs); // fa il merge in automatico

        //$currentQueries = $request->query();
        //Declare new queries you want to append to string:
        //$newQueries = ['year' => date('Y')];
        //Merge together current and new query strings:
        //$allQueries = array_merge($currentQueries, $qs);
        //Generate the URL with all the queries:
        //return $request->fullUrlWithQuery($allQueries);

    }
    */

    //-- nella registrazione 1 tasto, nelle modifiche 3

    public function btnHtml(array $params): ?string {
        return $this->form->{__FUNCTION__}($params);
    }

    public function btnCrud(array $params = []): ?string {
        return $this->form->{__FUNCTION__}($params);
    }

    public function imageHtml(array $params): string { //usare PanelImageService
        /*
        * mettere imageservice, o quello di spatie ?
        *
        **/
        $params['src'] = $this->row->image_src;
        $img = new ImageService($params);
        $src = $img->fit()->save()->src();

        return '<img src="'.asset($src).'" >';
    }

    public function imgSrc(array $params): string { //usare PanelImageService
        $row = $this->row;
        if (! is_object($row)) {
            return '#'.__LINE__.class_basename($this);
        }
        $src = $row->image_src;

        $str0 = '/laravel-filemanager/';
        if (Str::startsWith($src, $str0)) {
            $src = '/'.Str::after($src, $str0);
        }
        $params['src'] = $src;
        $width = 200;
        $height = 200;
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
            //dddx($src_out);
            return $src_out;
        } catch (\Exception $e) {
            //dddx('isolated');
            return '#['.__LINE__.']['.__FILE__.']';
        }
    }

    /**
     * @return string
     */
    public function microdataSchemaOrg() {
        return '';
    }

    /**
     * @return array
     */
    public function show_ldJson() {
        return [];
    }

    /**
     * @param string $lang
     *
     * @return string
     */
    public function langUrl($lang) {
        $params = [];
        $params['lang'] = $lang;
        $params['panel'] = $this;

        return $this->route->urlLang($params);
    }

    /**
     * @param array $params
     *
     * @return string
     */
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

    public function relatedUrl(array $params = []): string {
        $params['panel'] = $this;

        return $this->route->urlRelatedPanel($params);
    }

    public function relatedName(string $name, ?int $id = null): ?PanelContract {
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

    public function url(array $params = []): string {
        $act = 'show';
        extract($params);
        $act = Str::snake($act);
        if (! isset($route_params)) {
            $route_params = null;
        }

        return $this->route->urlPanel([/*'panel' => $this,*/ 'act' => $act]);
    }

    /**
     * @return string
     */
    public function indexUrl() {
        //$url = $this->route->urlModel(['model' => $this->row, 'panel_parent' => $this->parent, 'act' => 'index']);
        $url = $this->route->urlPanel(['panel' => $this, 'act' => 'index']);
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

    /**
     * @return string|void
     */
    public function indexEditUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'index_edit']);
    }

    /**
     * @return string|void
     */
    public function editUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'edit']);
    }

    /**
     * @return string|void
     */
    public function updateUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'update']);
    }

    /**
     * @return string|void
     */
    public function showUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'show']);
    }

    /**
     * @return string|void
     */
    public function createUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'create']);
    }

    /**
     * @return string|void
     */
    public function storeUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'store']);
    }

    /**
     * @return string|void
     */
    public function destroyUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'destroy']);
    }

    /**
     * @return string|void
     */
    public function detachUrl() {
        return $this->route->urlPanel(['panel' => $this, 'act' => 'detach']);
    }

    /**
     * @return string
     */
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

    /**
     * @return false|mixed|string
     */
    public function postType() {
        $post_type = collect(config('xra.model'))->search(get_class($this->row));
        if (false === $post_type) {
            $post_type = snake_case(class_basename($this->row));
        }

        return $post_type;
    }

    /**
     * @return mixed|string|null
     */
    public function guid(?bool $is_admin = null) {
        if (isset($is_admin) && $is_admin) {
            return $this->row->getKey();
        }
        if (null !== $this->getInAdmin() && $this->getInAdmin()) {
            return $this->row->getKey();
        }
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
        if ('' == $guid && method_exists($row, 'post') && 'guid' == $key && property_exists($row, 'post')) {
            //if ('' == $row->id && '' != $row->post_id) {
            //    $row->id = $row->post_id; //finche netson non riabilita migrazioni
            //}
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

    /**
     * @return array[]
     */
    public function getItemTabs() {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    /**
     * @return array
     */
    public function getRowTabs() {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    public function getTabs(): array {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function rows(?array $data = null) {
        if (null == $data) {
            $data = request()->all();
        }
        $filters = $data;
        $q = isset($data['q']) ? $data['q'] : null;
        $out_format = isset($data['format']) ? $data['format'] : null;
        $sort = isset($data['sort']) ? $data['sort'] : null;
        //$act = isset($data['_act']) ? $data['_act'] : null;

        $query = $this->rows;
        /*
        dddx([
            'getQuery' => $query->getQuery(),
            'methods' => get_class_methods($query),
        ]);
        */
        if (! is_object($query)) {
            if (! is_object($this->row)) {
                return $query;
            }
            $query = $this->row;
        }

        //dddx(get_class($this));
        $with = $this->with();
        //dddx($with);
        $query = $query->with($with); //Method Illuminate\Database\Eloquent\Collection::with does not exist.
        /*
        * se prendo il builder perdo il modello.
        */
        //$query = $query->getQuery(); //per prendere il Illuminate\Database\Query\Builder

        $query = $this->indexQuery($data, $query);

        //$query=$query->withPost('a');
        //$query = $this->applyJoin($query);
        $query = $this->applyFilter($query, $filters);

        $query = $this->applySearch($query, $q);

        //dddx(Route::is('*edit*'));
        //controllo se la pagina su cui devo andare è un edit,
        //in caso tolgo i sort che rompono le scatole
        //magari meglio fare helper?
        if (! Route::is('*edit*')) {
            $query = $this->applySort($query, $sort);
        }
        //$query = $this->applySort($query, $sort);

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

    /**
     * @param string $act
     *
     * @return null
     */
    public function callItemActionWithGate($act) {
        //$actions = $this->actions();
        //dddx([get_class($this), $actions]);
        $method_act = Str::camel($act);
        $authorized = Gate::allows($method_act, $this);

        if (! $authorized) {
            return $this->notAuthorized($method_act);
        }

        return $this->callItemAction($act);
    }

    public function notAuthorized(string $method) {
        $policy_class = PolicyService::get($this)->createIfNotExists()->getClass();
        $msg = 'Auth Id ['.\Auth::id().'] not can ['.$method.'] on ['.$policy_class.']';

        if (! view()->exists('pub_theme::errors.403')) {
            return '<h3> Aggiungere la view : pub_theme::errors.403<br/>pub_theme: '.config('xra.pub_theme').'</h3>';
        }

        return response()->view('pub_theme::errors.403', ['msg' => $msg], 403);
    }

    /**
     * @param string $act
     *
     * @return mixed
     */
    public function callAction($act) {
        //$act = Str::camel($act);

        $action = $this->getActions()
            ->firstWhere('name', $act);
        if (! is_object($action)) {
            $msg = 'action '.$act.' not recognized for ['.get_class($this).']';

            return response()->view('pub_theme::errors.403', ['msg' => $msg], 403);
        }

        $action->setRow($this->row);
        $rows = $this->rows();
        $action->setRows($rows);

        $action->setPanel($this);

        $method = request()->getMethod();
        if ('GET' == $method) {
            return $action->handle();
        } else {
            return $action->postHandle();
        }
    }

    /**
     * @param string $act
     *
     * @return null
     */
    public function callItemAction($act) {
        if (null == $act) {
            return null;
        }
        $action = $this->itemActions()
            ->firstWhere('name', $act);
        if (! is_object($action)) {
            $msg = '<h3>['.$act.'] not exists in ['.get_class($this).']</h3>Actions Avaible are :';
            foreach ($this->itemActions() as $act) {
                $msg .= '<br/>'.$act->getName();
            }

            return $msg;
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

    /**
     * @param string $act
     *
     * @return null
     */
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

    /**
     * @return mixed
     */
    public function out(array $params = []) {
        //dddx(get_class($this->presenter));//Modules\Xot\Presenters\HtmlPanelPresenter

        return $this->presenter->out();
    }

    /**
     * @return string
     */
    public function pdfFilename(array $params = []) {
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

    public function xls(array $params = []) {
        $presenter = new XlsPanelPresenter();
        $presenter->setPanel($this);
        //dddx($this->rows()->get()->count());

        return $presenter->out($params);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|void
     */
    public function pdf(array $params = []) {
        $presenter = new PdfPanelPresenter();
        $presenter->setPanel($this);
        //dddx($this->rows()->get()->count());

        return $presenter->out($params);
        /*
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

        //dddx($this->rows->get());
        if (request()->input('debug')) {
            return $html;
        }
        $params['html'] = (string) $html;

        return HtmlService::toPdf($params);
        */
    }

    //Method Modules\Xot\Models\Panels\XotBasePanel::related() should return Modules\Xot\Models\Panels\XotBasePanel but returns Modules\Xot\Contracts\PanelContract|null.
    public function related(string $relationship): PanelContract {
        $related = $this->row->$relationship()->getRelated();
        $panel_related = Panel::get($related);
        $panel_related->setParent($this);

        return $panel_related;
    }

    public function getModuleName(): string {
        $model = $this::$model;
        $module_name = Str::before(Str::after($model, 'Modules\\'), '\\Models\\');

        return $module_name;
    }

    public function getModuleNameLow(): string {
        return Str::lower($this->getModuleName());
    }

    public function breadcrumbs(): array {
        return [];
        /*
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
        */
    }

    /**
     * @param int $length
     *
     * @return string
     */
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

    /**
     * @return array
     */
    public function indexEditSubs() {
        return [];
    }

    /**
     * @return string
     */
    public function swiperItem() {
        return 'pub_theme::layouts.swiper.item';
    }

    /**
     * @return mixed
     */
    public function view(?array $params = null) {
        return $this->presenter->out($params);
    }

    /**
     * @return string
     */
    public function id(?bool $is_admin = null) {
        $curr = $this;
        $data = collect([]);
        while (null != $curr) {
            //$data->prepend($curr->postType().'-'.$curr->guid($is_admin));
            $data->prepend($curr->postType().'-'.$curr->row->getKey());

            $curr = $curr->getParent();
        }

        return $data->implode('-');
    }

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function tabs() {
        return [];
    }

    /**
     * @return array
     */
    public function actions() {
        return [];
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function update($data) {
        //$func = '\Modules\Xot\Jobs\Crud\\'.Str::studly(__FUNCTION__).'Job';
        $func = '\Modules\Xot\Jobs\PanelCrud\\'.Str::studly(__FUNCTION__).'Job';

        $panel = $func::dispatchNow($data, $this);

        return $panel;
    }
}

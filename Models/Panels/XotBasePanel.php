<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
//----------  SERVICES --------------------------
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Contracts\RowsContract;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Presenters\PdfPanelPresenter;
use Modules\Xot\Presenters\XlsPanelPresenter;
use Modules\Xot\Services\ChainService;
use Modules\Xot\Services\ImageService;
use Modules\Xot\Services\PanelActionService;
use Modules\Xot\Services\PanelFormService;
use Modules\Xot\Services\PanelRouteService;
use Modules\Xot\Services\PanelService;
use Modules\Xot\Services\PanelService as Panel;
use Modules\Xot\Services\PanelTabService;
use Modules\Xot\Services\PolicyService;
use Modules\Xot\Services\StubService;
use Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class XotBasePanel.
 *
 * Modules\Xot\Models\Panels\XotBasePanel.
 */
abstract class XotBasePanel implements PanelContract
{
    protected static string $model;

    public Model $row;

    //e se fosse relation ?
    //Typed property Modules\Xot\Models\Panels\XotBasePanel::$rows must not be accessed before initialization

    /**
     * Undocumented variable.
     */
    //public Relation $rows;
    public RowsContract $rows;

    public ?Builder $builder = null;

    public string $name;

    public ?PanelContract $parent = null;

    public ?bool $in_admin = null;

    public array $route_params = [];

    public PanelPresenterContract $presenter;

    public PanelFormService $form;

    public PanelRouteService $route;

    public function __construct(PanelPresenterContract $presenter, PanelRouteService $route)
    {
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
     */
    public function with(): array
    {
        return [];
    }

    public function setBuilder(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    public function getBuilder(): Builder
    {
        if (null != $this->builder) {
            return $this->builder;
        }
        //143    Call to an undefined method Illuminate\Database\Eloquent\Relations\Relation::with().
        //return $this->rows->with();
        //145    Call to an undefined method Illuminate\Database\Eloquent\Relations\Relation::where().
        //return $this->rows->where('1=1');
        return $this->rows->getQuery(); //Get the underlying query for the relation.
        //return $this->rows->getBaseQuery();//Get the base query builder driving the Eloquent builder.
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        if (null != $this->name) {
            return $this->name;
        } else {
            return $this->postType();
        }
    }

    public function setRow(Model $row): self
    {
        $this->row = $row;

        return $this;
    }

    //public function setRows(Builder $rows): self {

    /**
     * Undocumented function.
     */
    //public function setRows(Relation $rows): self {
    public function setRows(RowsContract $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /*
     * get Row.
     *
     */
    public function getRow(): Model
    {
        return $this->row;
    }

    /**
     * get Rows.
     */
    //public function getRows(): Relation
    public function getRows(): RowsContract
    {
        if (null == $this->rows) {
            dddx(debug_backtrace());
            throw new \Exception('rows is null [line:'.__LINE__.'][class:'.get_class($this).']');
        }

        /*
        if (null == $this->rows) {
            return $this->row->query();
        }
        */
        return $this->rows;
    }

    /* deprecated
    public function initRows(): self {
        $this->rows = $this->rows();
        //$this->rows = $this->rows; ??

        return $this;
    }
    */

    //Parameter #1 $panel of method Modules\Xot\Contracts\PanelContract::setParent()
    //expects Modules\Xot\Contracts\PanelContract,
    //        Modules\Xot\Contracts\PanelContract|null given.
    public function setParent(?PanelContract $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function setBrother(PanelContract $panel): self
    {
        $this->setParent($panel->getParent());
        $this->setName($panel->getName());

        return $this;
    }

    // se uso in rows() getQuery il dato ottenuto e' una collezione di items non di modelli
    public function getHydrate(object $data): PanelContract
    {
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

    public function getParent(): ?PanelContract
    {
        return $this->parent;
    }

    public function getParents(): Collection
    {
        $parents = collect([]);
        $panel_curr = $this->getParent();
        while (null != $panel_curr) {
            $parents->prepend($panel_curr);
            $panel_curr = $panel_curr->getParent();
        }

        return $parents;
    }

    public function getBreads(): Collection
    {
        $breads = $this->getParents();
        $breads->add($this);

        return $breads;
    }

    // questa funzione rilascia un array dei guid dell'url attuale
    public function getParentsGuid(): array
    {
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
    public function findParentType(string $type)
    {
        return collect($this->getParents())->filter(
            function ($item) use ($type) {
                return $type == $item->postType();
            }
        )->first();
    }

    /**
     * @return mixed
     */
    public function optionId(object $row)
    {
        return $row->getKey();
    }

    /*
     * @return void
     */
    public function setInAdmin(?bool $in_admin): void
    {
        $this->in_admin = $in_admin;
    }

    /*
     * @return bool|null
     */
    public function getInAdmin(): ?bool
    {
        return $this->in_admin;
    }

    public function setRouteParams(array $route_params): void
    {
        $this->route_params = $route_params;
    }

    public function getRouteParams(): array
    {
        $route_current = Route::current();
        $route_params = is_object($route_current) ? $route_current->parameters() : [];

        $route_params = array_merge($route_params, $this->route_params);

        return $route_params;
    }

    public function setItem(string $guid): self
    {
        $row = $this->row;
        $rows = $this->getBuilder();
        $tbl = $row->getTable();
        //$pk = $model->getRouteKeyName($this->in_admin);
        $pk = $row->getRouteKeyName(); // !!! MI SEMBRA STRANO !!
        $pk_full = $row->getTable().'.'.$pk;

        if ('guid' == $pk) {
            $pk_full = 'guid';
        } // pezza momentanea

        $value = Str::slug($guid); //retrocompatibilita'
        if ('guid' == $pk_full) {
            // 301    Call to an undefined method Illuminate\Database\Eloquent\Builder|Illuminate\Database\Eloquent\Relations\Relation::whereHas().
            $rows = $rows->whereHas(
                'posts',
                function (Builder $query) use ($value): void {
                    $query->where('guid', $value);
                }
            );
        } else {
            $rows = $rows->where([$pk_full => $value]);
        }

        $row = $rows
            //->select($tbl.'.*')
            //->select('cuisine_cat_morph.note as "pivot.note"')
            ->first();

        if (null == $row) {
            throw new \Exception('Not Found ['.$value.'] on ['.$this->getName().']');
        }
        $this->row = $row;

        return $this;
    }

    //funzione/flag da settare a true ad ogni pannello/modello che abbia le traduzioni

    /**
     * @return false
     */
    public function hasLang()
    {
        return false;
    }

    public function setLabel(string $label): Model
    {
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
        if (!method_exists($model, 'post')) {
            throw new \Exception('in ['.get_class($model).'] method [post] is missing');
        }
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
    public function optionLabel(object $row): string
    {
        return $row->matr.' ['.$row->email.']['.$row->ha_diritto.'] '.$row->cognome.' '.$row->cognome.' ';
    }

    public function title(): ?string
    {
        return optional($this->row)->title;
    }

    /**
     * @return array
     */
    public function optionsSelect()
    {
        $opts = [];
        $rows = $this->getBuilder()->get();

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
    public function options($data = null)
    {
        if (null == $data) {
            $data = request()->all();
        }
        //dddx($this->rows()->get());

        return $this->rows($data)->get();
    }

    public function optionsTree(array $data = []): array
    {
        if (null == $data /*|| empty($data)*/) {
            $data = request()->all();
        }

        //$rows = $this->rows($data)->get();
        $rows = $this->getBuilder()->get();

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
    public function optionIdName()
    {
        return $this->row->getKeyName();
    }

    /**
     * @return string
     */
    public function optionLabelName()
    {
        return 'matr';
    }

    /**
     * inserisco i campi dove si applicherà la funzione di ricerca testuale (solo nel pannello admin?)
     * se il pannello interessato rilascia un array vuoto, fa la ricerca tra i fillable del modello
     * esempio post.title, post.subtitle.
     */
    public function search(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function orderBy()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getOrderField()
    {
        return $this->row->getKeyName();
    }

    /**
     * Get the actions available for the resource.
     */
    public function fields(): array
    {
        return [];
    }

    public function rules(array $params = []): array
    {
        $act = '';
        extract($params);
        if ('' == $act) {
            $route_action = (string) \Route::currentRouteAction();
            $act = Str::after($route_action, '@');
        }
        switch ($act) {
            case 'store':
                $fields = $this->getFields(['act' => 'create']);
                break;
            case 'update':
                $fields = $this->getFields(['act' => 'edit']);
                break;
            default:
                $fields = $this->fields();
                break;
        }
        $act = request()->input('_act');
        if ('' != $act) {
            $fields = collect($fields)->filter(
                function ($item) use ($act) {
                    if (!isset($item->except)) {
                        $item->except = [];
                    }

                    return !in_array($act, $item->except);
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
                if (!isset($item->rules)) {
                    $item->rules = '';
                }
                if (Str::contains($item->type, 'RelationshipOne')) {
                    $rows_tmp = $this->row->{$item->name}();
                    /*
                    if (! is_object($rows_tmp)) {
                        throw new \Exception('rows_tmp is not an object');
                    }
                    */
                    //529    Cannot call method getLocalKeyName() on class-string|object.

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
                    $pivot_panel->setRows(with(new $this::$model())->$rel_name());
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

    public function pivotRules(array $params = []): array
    {
        extract($params);

        return [];
    }

    public function rulesMessages(): array
    {
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
        if (!\is_array($rules_msg)) {
            $rules_msg = [];
        }
        $rules_msg_generic = trans('theme::generic');
        if (!\is_array($rules_msg_generic)) {
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
     */
    public function filters(Request $request = null): array
    {
        return [];
    }

    public function getXotModelName(): ?string
    {
        return collect(config('xra.model'))->search(static::$model);
    }

    /**
     * index Navigation.
     *
     * @return null
     */
    public function indexNav(): ?Renderable
    {
        return null;
    }

    public function getActions(array $params = []): Collection
    {
        return (new PanelActionService($this))->{__FUNCTION__}($params);
    }

    public function containerActions(array $params = []): Collection
    {
        return (new PanelActionService($this))->{__FUNCTION__}($params);
    }

    public function itemActions(array $params = []): Collection
    {
        return (new PanelActionService($this))->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function itemAction(string $act)
    {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    /**
     * @return mixed
     */
    public function containerAction(string $act)
    {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    /**
     * @return mixed
     */
    public function urlContainerAction(string $act)
    {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    /**
     * @return mixed
     */
    public function urlItemAction(string $act)
    {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    /**
     * @return mixed
     */
    public function btnItemAction(string $act)
    {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    //-- nella registrazione 1 tasto, nelle modifiche 3
    //public function btnSubmit() {
    //return Form::bsSubmit('save');
    //    return Form::bsSubmit(trans('xot::buttons.save'));
    //}

    /**
     * Build an "index" query for the given resource.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public static function indexQuery(array $data, $query)
    {
        //return $query->where('auth_user_id', $request->user()->auth_user_id);
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public static function relatableQuery(Request $request, $query)
    {
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

    //|\Illuminate\Database\Query\Builder

    /**
     * Undocumented function.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public function applyFilter($query, array $filters)
    {
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
                if (!isset($v->op)) {
                    $v->op = '=';
                }
                if (isset($v->where_method)) {
                    if (!isset($v->field_name)) {
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
     */

    /**
     * Undocumented function.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public function applySearch($query, ?string $q)
    {
        if (!isset($q)) {
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
     * Undocumented function.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public function applySort($query, ?array $sort)
    {
        if (!is_array($sort)) {
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

    /**
     * @return mixed
     */
    public function formCreate(array $params = [])
    {
        return $this->form->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function formEdit(array $params = [])
    {
        return $this->form->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function formLivewireEdit(array $params = [])
    {
        return $this->form->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function getFormData(array $params = [])
    {
        return $this->form->{__FUNCTION__}($params);
    }

    public function editObjFields(): array
    {
        return $this->form->{__FUNCTION__}();
    }

    public function getFields(array $params = []): array
    {
        return $this->form->{__FUNCTION__}($params);
    }

    public function btnHtml(array $params): string
    {
        return $this->form->{__FUNCTION__}($params);
    }

    public function btnCrud(array $params = []): string
    {
        return $this->form->{__FUNCTION__}($params);
    }

    public function imageHtml(array $params): string
    { //usare PanelImageService
        /*
        * mettere imageservice, o quello di spatie ?
        *
        **/
        if (!property_exists($this->row, 'image_src')) {
            throw new \Exception('in ['.get_class($this->row).'] property [image_src] is missing');
        }
        $params['src'] = $this->row->image_src;
        $img = new ImageService($params);
        $src = $img->fit()->save()->src();
        if (!is_string($src)) {
            throw new \Exception('src is not a string');
        }

        return '<img src="'.asset($src).'" >';
    }

    public function imgSrc(array $params): string
    {
        $params['dirname'] = '/photos/'.$this->postType().'/'.$this->guid();
        $params['src'] = optional($this->row)->image_src;
        $img = new ImageService($params);

        return $img->url();
    }

    public function microdataSchemaOrg(): string
    {
        return '';
    }

    public function show_ldJson(): array
    {
        return [];
    }

    public function relatedUrl(array $params = []): string
    {
        return $this->route->{__FUNCTION__}($params);
    }

    public function langUrl(string $lang): string
    {
        return $this->route->{__FUNCTION__}(['lang' => $lang]);
    }

    public function url(array $params = []): string
    {
        return $this->route->{__FUNCTION__}($params);
    }

    public function relatedName(string $name, ?int $id = null): PanelContract
    {
        //bell_boy => Modules\Food\Models\BellBoy
        $model = xotModel($name);
        if (null != $id) {
            $model = $model->find($id);
        }
        $panel = Panel::get($model);
        //if (! is_object($panel)) {

        //    return null;
        //}
        $panel = $panel->setParent($this);

        return $panel;
    }

    public function postType(): string
    {
        $post_type = collect(config('xra.model'))->search(get_class($this->row));
        if (false === $post_type) {
            $post_type = snake_case(class_basename($this->row));
        }

        return $post_type;
    }

    public function guid(?bool $is_admin = null): ?string
    {
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
            //'$row->$key' => $row->{$key},
            //'$row->post' => $row->post,
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

        return (string) $guid;
    }

    /*
    public function getTitle() {
       $name = $this->getName();
       //$title = str_replace('_', ' ', $title);
       $row = $this->panel->getRow();

       $module_name_low = strtolower(getModuleNameFromModel($row));
       $title = trans($module_name_low.'::'.strtolower(class_basename($row)).'.act.'.$name);

       return $title;
    }
    */

    public function getItemTabs(): array
    {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    public function getRowTabs(): array
    {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    public function getTabs(): array
    {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    //Return value of Modules\Xot\Models\Panels\XotBasePanel::rows()
    //must be an instance of Illuminate\Database\Eloquent\Builder,
    //instance of Illuminate\Database\Eloquent\Relations\MorphToMany returned

    /**
     * Undocumented function.
     *
     * @return RowsContract
     */
    public function rows(?array $data = null)
    {
        if (null == $data) {
            $data = request()->all();
        }
        $filters = $data;
        $q = isset($data['q']) ? $data['q'] : null;
        $sort = isset($data['sort']) ? $data['sort'] : null;
        $query = $this->getRows();
        //$query = $this->getBuilder();

        $with = $this->with();
        if (method_exists($query, 'with')) {
            $query = $query->with($with);
        }
        /*
        try {
            $query = $query->with($with);
        } catch (\Exception $e) {
            //Method Illuminate\Database\Eloquent\Collection::with does not exist.
        }
        */
        $query = $this->indexQuery($data, $query);
        $query = $this->applyFilter($query, $filters);
        $query = $this->applySearch($query, $q);

        if (!Route::is('*edit*')) {
            $query = $this->applySort($query, $sort);
        }
        /*
        $page = isset($data['page']) ? $data['page'] : 1;
        Cache::forever('page', $page);
        */
        return $query;
    }

    /**
     * @return mixed
     */
    public function callItemActionWithGate(string $act)
    {
        //$actions = $this->actions();
        //dddx([get_class($this), $actions]);
        $method_act = Str::camel($act);
        $authorized = Gate::allows($method_act, $this);

        if (!$authorized) {
            return $this->notAuthorized($method_act);
        }

        return $this->callItemAction($act);
    }

    public function notAuthorized(string $method): \Illuminate\Http\Response
    {
        $policy_class = PolicyService::get($this)->createIfNotExists()->getClass();
        $msg = 'Auth Id ['.\Auth::id().'] not can ['.$method.'] on ['.$policy_class.']';

        if (!view()->exists('pub_theme::errors.403')) {
            $msg = '<h3> Aggiungere la view : pub_theme::errors.403<br/>pub_theme: '.config('xra.pub_theme').'</h3>';
            throw new \Exception($msg);
        }

        return response()->view('pub_theme::errors.403', ['message' => $msg], 403);
    }

    /**
     * @return mixed
     */
    public function callAction(string $act)
    {
        //$act = Str::camel($act);

        $action = $this->getActions()
            ->firstWhere('name', $act);
        if (!is_object($action)) {
            $msg = 'action '.$act.' not recognized for ['.get_class($this).']';

            return response()->view('pub_theme::errors.403', ['message' => $msg], 403);
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
     * @return mixed
     */
    public function callItemAction(string $act)
    {
        if (null == $act) {
            return null;
        }
        $action = $this->itemActions()
            ->firstWhere('name', $act);
        if (!is_object($action)) {
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
     * @return mixed
     */
    public function callContainerAction(string $act)
    {
        if (null == $act) {
            return null;
        }
        $action = $this->containerActions()
            ->firstWhere('name', $act);
        if (!is_object($action)) {
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
    public function out(array $params = [])
    {
        try {
            return $this->presenter->out();
        } catch (\Exception $e) {
            return response()->view('pub_theme::errors.500', ['message' => $e->getMessage()], 500);
        }
    }

    /*//--- valutare
    public function __toString() {
        return $this->presenter->out()->render();
    }
    */

    public function pdfFilename(array $params = []): string
    {
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

    /**
     * Undocumented function.
     *
     * @return mixed
     */
    public function xls(array $params = [])
    {
        $presenter = new XlsPanelPresenter();
        $presenter->setPanel($this);
        //dddx($this->rows()->get()->count());

        return $presenter->out($params);
    }

    /**
     * Undocumented function.
     *
     * @return mixed
     */
    public function pdf(array $params = [])
    {
        $presenter = new PdfPanelPresenter();
        $presenter->setPanel($this);

        return $presenter->out($params);
    }

    //Method Modules\Xot\Models\Panels\XotBasePanel::related() should return Modules\Xot\Models\Panels\XotBasePanel but returns Modules\Xot\Contracts\PanelContract|null.
    public function related(string $relationship): PanelContract
    {
        $related = $this->row->$relationship()->getRelated();
        $panel_related = Panel::get($related);
        $panel_related->setParent($this);

        return $panel_related;
    }

    public function getModuleName(): string
    {
        $model = $this::$model;
        $module_name = Str::before(Str::after($model, 'Modules\\'), '\\Models\\');

        return $module_name;
    }

    public function getModuleNameLow(): string
    {
        return Str::lower($this->getModuleName());
    }

    public function breadcrumbs(): array
    {
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
            $tmp->url = $parent->url(['act'=>'index']);
            $tmp->title = $parent->postType();
            $tmp->obj = \Theme::xotModel($tmp->title);
            $tmp->method = 'index';
            $bread[] = $tmp;
            try {
                $tmp = (object) [];
                $tmp->url = $parent->url(['act'=>'show']);
                $tmp->title = $parent->getRow()->title;
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
    public function getExcerpt($length = 225)
    {
        $row = $this->row;
        //$content = $row->subtitle ?? $row->txt;
        if (!property_exists($row, 'subtitle')) {
            throw new \Exception('in ['.get_class($row).'] property [subtitle] is missing');
        }
        if (!property_exists($row, 'txt')) {
            throw new \Exception('in ['.get_class($row).'] property [txt] is missing');
        }

        if ($row->subtitle) {
            $content = $row->subtitle;
        } else {
            $content = $row->txt;
        }

        // 1737   Parameter #1 $str of function strip_tags expects string, array|string|null given.
        $tmp = preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content);
        if (is_array($tmp)) {
            $tmp = implode(' ', $tmp);
        }
        if (null == $tmp) {
            $tmp = '';
        }
        $cleaned = strip_tags($tmp, '<code>');
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
    public function indexEditSubs()
    {
        return [];
    }

    /**
     * @return string
     */
    public function swiperItem()
    {
        return 'pub_theme::layouts.swiper.item';
    }

    /**
     * @return mixed
     */
    public function view(?array $params = null)
    {
        return $this->presenter->out($params);
    }

    public function id(?bool $is_admin = null): string
    {
        $curr = $this;
        $data = collect([]);
        while (null != $curr) {
            //$data->prepend($curr->postType().'-'.$curr->guid($is_admin));
            $data->prepend($curr->postType().'-'.$curr->getRow()->getKey());

            $curr = $curr->getParent();
        }

        return $data->implode('-');
    }

    /**
     * Get the tabs available.
     */
    public function tabs(): array
    {
        return [];
    }

    /**
     * Get the actions available for the resouce.
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function update(array $data)
    {
        //$func = '\Modules\Xot\Jobs\Crud\\'.Str::studly(__FUNCTION__).'Job';
        $func = '\Modules\Xot\Jobs\PanelCrud\\'.Str::studly(__FUNCTION__).'Job';

        $panel = $func::dispatchNow($data, $this);

        return $panel;
    }

    public function isRevisionBy(UserContract $user): bool
    {
        $post = $this->getRow();
        if ($post->getAttributeValue('created_by') == $user->handle ||
            $post->getAttributeValue('updated_by') == $user->handle ||
            $post->getAttributeValue('auth_user_id') == $user->auth_user_id) {
            return true;
        }

        return false;
    }
}

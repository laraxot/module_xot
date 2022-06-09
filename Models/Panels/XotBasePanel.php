<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Panels;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
// ----------  SERVICES --------------------------
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Theme\Services\FieldService;
use Modules\Xot\Contracts\ModelWithAuthorContract;
use Modules\Xot\Contracts\PanelContract;
use Modules\Xot\Contracts\PanelPresenterContract;
use Modules\Xot\Contracts\RowsContract;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Panels\Actions\XotBasePanelAction;
use Modules\Xot\Presenters\PdfPanelPresenter;
use Modules\Xot\Presenters\XlsPanelPresenter;
use Modules\Xot\Services\ChainService;
use Modules\Xot\Services\FileService;
use Modules\Xot\Services\ImageService;
use Modules\Xot\Services\PanelActionService;
use Modules\Xot\Services\PanelFormService;
use Modules\Xot\Services\PanelRouteService;
use Modules\Xot\Services\PanelService;
use Modules\Xot\Services\PanelTabService;
use Modules\Xot\Services\PolicyService;
use Modules\Xot\Services\RouteService;
use Modules\Xot\Services\RowsService;
use Modules\Xot\Services\StubService;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class XotBasePanel.
 *
 * Modules\Xot\Models\Panels\XotBasePanel.
 */
abstract class XotBasePanel implements PanelContract {
    protected static string $model;

    // public Model $row;

    // e se fosse relation ?
    // Typed property Modules\Xot\Models\Panels\XotBasePanelService::$rows must not be accessed before initialization

    /**
     * Undocumented variable.
     */
    // public Relation $rows;
    // public RowsContract $rows;

    /**
     * in certe relazioni c'e' il where se passo al builder perdo i "with".
     *
     * @var Relation|Builder
     */
    public $rows;

    public ?Builder $builder = null;

    public ?string $name = null;

    public ?PanelContract $parent = null;

    public ?bool $in_admin = null;

    public array $route_params = [];

    public PanelPresenterContract $presenter;

    public PanelFormService $form;

    public PanelRouteService $route;

    public function __construct(PanelPresenterContract $presenter, PanelRouteService $route) {
        $this->presenter = $presenter->setPanel($this);

        // $this->row = app($this::$model);
        // $this->form = app(PanelFormService::class)->setPanel($this);
        $this->form = new PanelFormService($this);
        $this->route = $route->setPanel($this);
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
    public function with(): array {
        return [];
    }

    public function setBuilder(Builder $builder): self {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Undocumented function.
     * ret_old also |\Illuminate\Database\Query\Builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder() {
        if (null !== $this->builder) {
            return $this->builder;
        }
        // 143    Call to an undefined method Illuminate\Database\Eloquent\Relations\Relation::with().
        // return $this->rows->with();
        // 145    Call to an undefined method Illuminate\Database\Eloquent\Relations\Relation::where().
        // return $this->rows->where('1=1');
        // return $this->rows->getQuery(); //Get the underlying query for the relation.

        $res = $this->getRows()->getQuery();
        if (! $res instanceof \Illuminate\Database\Eloquent\Builder) {
            throw new Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        return $res;

        // return $this->rows->getBaseQuery();//Get the base query builder driving the Eloquent builder.
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getName(): string {
        if (null !== $this->name) {
            return $this->name;
        } else {
            return $this->postType();
        }
    }

    /**
     * Undocumented function.
     *
     * @param Model $row
     */
    public function setRow($row): self {
        $this->row = $row;

        /*--- in teoria con la "&"
        $this->form->setPanel($this);
        $this->route->setPanel($this);
        */

        return $this;
    }

    // public function setRows(Builder $rows): self {

    /**
     * Undocumented function.
     *
     * @param mixed $rows
     */
    public function setRows($rows): self {
        $this->rows = $rows;

        return $this;
    }

    /*
     * get Row.
     *
     */
    public function getRow(): Model {
        return $this->row;
    }

    /**
     * get Rows.
     *
     * @return Relation|Builder
     */
    public function getRows() {
        if (null === $this->rows) {
            // throw new \Exception('rows is null [line:'.__LINE__.'][class:'.get_class($this).']');
            // nel caso di stampare un pdf non serve avere le rows
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

    // Parameter #1 $panel of method Modules\Xot\Contracts\PanelContract::setParent()
    // expects Modules\Xot\Contracts\PanelContract,
    //        Modules\Xot\Contracts\PanelContract|null given.
    public function setParent(?PanelContract $panel): self {
        $this->parent = $panel;

        return $this;
    }

    public function setBrother(PanelContract $panel): self {
        $this->setParent($panel->getParent());
        $this->setName($panel->getName());

        return $this;
    }

    public function getSonModel(Model $model): PanelContract {
        $panel = PanelService::make()->get($model)->setParent($this);
        $name = Str::plural($panel->postType());
        $panel->setName($name);

        return $panel;
    }

    public function newPanel(Model $row): self {
        $cloned = clone $this;
        $cloned->setRow($row);
        $cloned->form->setPanel($cloned);
        $cloned->route->setPanel($cloned);

        return $cloned;
    }

    /**  se uso in rows() getQuery il dato ottenuto e' una collezione di items non di modelli.
     *
     */
    /* non usata ?
    public function getHydrate($data): PanelContract {
        if ('stdClass' == get_class($data)) {
            //$row = $this->row->hydrate((array) $data);
            $row = $this->row->forceFill((array) $data);
        } else {
            $row = $data;
        }
        $panel = PanelService::make()->get($row);
        $panel->setParent($this->getParent());

        return $panel;
    }
    */

    /**
     * getParent.
     */
    public function getParent(): ?PanelContract {
        return $this->parent;
    }

    /**
     * Undocumented function.
     *
     * @return Collection&iterable<PanelContract>
     */
    public function getParents() {
        $parents = collect([]);
        $panel_curr = $this->getParent();
        while (null !== $panel_curr) {
            $parents->prepend($panel_curr);
            $panel_curr = $panel_curr->getParent();
        }

        return $parents;
    }

    /**
     * @return Collection&iterable<PanelContract>
     */
    public function getBreads():Collection {
        /**
         * @var string
         */
        $class=class_basename($this);
        /**
         * @var array
         */
        $check=['_ModulePanel'/* 'HomePanel' */];

        $empty=[];
        if (in_array($class, $check, true)) {
            return collect($empty);
        }
        if ($this->getParents()->count() > 0 && \in_array($class, ['HomePanel'], true)) {
            return collect($empty);
        }
        $breads = $this->getParents();
        $breads->add($this);

        return $breads;
    }

    // questa funzione rilascia un array dei guid dell'url attuale
    /*
    public function getParentsGuid(): array {
        $parents = $this->getParents();
        $parent_first = $parents->first();
        // 300    Access to an undefined property object::$row.
        if (is_object($parent_first)) {
            while (null != $parent_first->row->parent) {
                $parent_first = PanelService::make()->get($parent_first->row->parent);
                $parents->prepend($parent_first);
            }
        }
        $parents_guid = $parents
            ->map(
                function ($item) {
                    return $item->row->guid;
                }
            )
            ->all();

        return $parents_guid;
    }
    */

    /**
     * @return mixed
     */
    public function findParentType(string $type) {
        return collect($this->getParents())->filter(
            function ($item) use ($type) {
                return $type === $item->postType();
            }
        )->first();
    }

    /**
     * @return int|string|null
     */
    public function optionId(Model $row) {
        return $row->getKey();
    }

    public function optionIdName(): string {
        return $this->row->getKeyName();
    }

    /**
     * on select the option label.
     */
    public function optionLabel(Model $row): string {
        // return $row->matr.' ['.$row->email.']['.$row->ha_diritto.'] '.$row->cognome.' '.$row->cognome.' ';
        return $row->getAttributeValue('title').''; // matr.' ['.$row->email.']['.$row->ha_diritto.'] '.$row->cognome.' '.$row->cognome.' ';
    }

    // public function optionLabelName():string {
    //    return 'matr';
    // }

    /*
     * ----
     */
    public function setInAdmin(?bool $in_admin): self {
        $this->in_admin = $in_admin;

        return $this;
    }

    public function getInAdmin(): ?bool {
        return $this->in_admin;
    }

    public function setRouteParams(array $route_params): void {
        $this->route_params = $route_params;
    }

    public function getRouteParams(): array {
        $route_current = Route::current();
        $route_params = \is_object($route_current) ? $route_current->parameters() : [];

        $route_params = array_merge($route_params, $this->route_params);

        return $route_params;
    }

    public function setItem(string $guid): self {
        $row = $this->row;
        $rows = $this->getBuilder();
        // $rows = $this->getRows();
        $tbl = $row->getTable();

        // 347    Method Illuminate\Database\Eloquent\Model::getRouteKeyName() invoked with 1 parameter, 0 required.
        // $pk = $row->getRouteKeyName($this->in_admin); //adesso restituisce guid, gli facciamo restituire "posts.guid" ?
        $pk = $row->getRouteKeyName(); // !!! MI SEMBRA STRANO !!
        $pk_full = $row->getTable().'.'.$pk;

        if ('guid' === $pk) {
            $pk_full = 'guid';
        } // pezza momentanea

        $value = Str::slug($guid); // retrocompatibilita'
        if ('guid' === $pk_full && method_exists($row, 'posts')) {
            // 301    Call to an undefined method Illuminate\Database\Eloquent\Builder|Illuminate\Database\Eloquent\Relations\Relation::whereHas().
            // if (! method_exists($rows, 'whereHas')) {
            //    throw new Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
            // }
            $builder = $rows;
            // if ($rows instanceof Relation) {
            //    $builder = $rows->getQuery();
            // }

            $rows = $builder->whereHas(
                'posts',
                function (Builder $query) use ($value): void {
                    $query->where('guid', $value);
                }
            );
        } else {
            // * phpstan rompe per il where dentro il customrelation
            // dddx($rows instanceof Relation);
            /*
            if (! method_exists($rows, 'where')) {
                throw new Exception('[class: '.class_basename($rows).'][method: where]['.__LINE__.']['.class_basename(__CLASS__).']');
            }
            //*/
            // try {
            $builder = $rows;
            // if ($rows instanceof Relation) {
            //    $builder = $rows->getQuery();
            // }
            $rows = $builder->where([$pk_full => $value]);
            /*
            } catch (Exception $e) {
                throw new Exception('
                    [message: '.$e->getMessage().']
                    [class: '.get_class($rows).']
                    [method: where]
                    ['.__LINE__.']['.class_basename(__CLASS__).']');
            }
            */
        }
        DB::enableQueryLog();
        $row = $rows
            // ->select($tbl.'.*')
            // ->select('cuisine_cat_morph.note as "pivot.note"')
            ->first();

        if (null === $row) {
            // $query = str_replace(array('?'), array('\'%s\''), $builder->toSql());
            // $query = vsprintf($query, $builder->getBindings());
            $sql = Str::replaceArray('?', $rows->getBindings(), $rows->toSql());
            throw new \Exception('Not Found ['.$value.'] on ['.$this->getName().']
                ['.$sql.']
                ['.__LINE__.']['.basename(__FILE__).']
                ');
        }
        $this->row = $row;

        return $this;
    }

    // funzione/flag da settare a true ad ogni pannello/modello che abbia le traduzioni (bandierina)
    public function hasLang(): bool {
        return false;
    }

    public function setLabel(string $label): Model {
        $model = $this->row;
        $res = $model::whereHas(
            'post',
            function (Builder $query) use ($label): void {
                $query->where('title', 'like', $label);
            }
        )->first();
        if (\is_object($res)) {
            return $res;
        }
        $me = $model->create();
        if (! method_exists($model, 'post')) {
            throw new \Exception('in ['.\get_class($model).'] method [post] is missing');
        }
        $post = $model->post()->create(
            [
                // 'post_id' => $me->getKey(),
                'title' => $label,
                'lang' => \App::getLocale(),
            ]
        );
        if (null === $post->post_id) {
            $post->post_id = $me->getKey();
            $post->save();
        }

        return $me;
    }

    public function title(): ?string {
        return $this->optionLabel($this->row);
    }

    public function txt(): ?string {
        return optional($this->row)->txt;
    }

    public function optionsModelClass(string $model_class, array $where = []): array {
        $data = [];

        $row = app($model_class);
        $panel = PanelService::make()->get($row);
        $with = $panel->with();
        $rows = $model_class::with($with)
            ->where($where)
            ->get();
        // $data[null]='---';
        foreach ($rows as $v) {
            $option_id = $panel->optionId($v);
            $option_label = $panel->optionLabel($v);
            $data[$option_id] = $option_label;
        }

        return $data;
    }

    public function optionsModelClassGrouped(string $model_class, string $group_by, array $where = []): array {
        $model = app($model_class);
        $panel = PanelService::make()->get($model);

        $options = $model->with($panel->with())
            ->where($where)
            ->get()
            ->groupBy($group_by)
            ->map(
                function ($item) use ($panel) {
                    $data = [];
                    foreach ($item as $v) {
                        $option_id = $panel->optionId($v);
                        $option_label = $panel->optionLabel($v);
                        $data[$option_id] = $option_label;
                    }

                    return $data;
                }
            )
            ->all();

        return $options;
    }

    /**
     * @return array
     */
    public function optionsSelect() {
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
    public function options($data = null) {
        if (null === $data) {
            $data = request()->all();
        }

        return $this->rows($data)->get();
    }

    public function optionsTree(array $data = []): array {
        /*
        if (null === $data  || empty($data) ) {
            $data = request()->all();
        }
        */

        $rows = $this->getBuilder()->get();

        $primary_field = $this->row->getKeyName();
        $c = new ChainService($primary_field, 'parent_id', 'pos', $rows);

        $options = collect($c->chain_table)->map(
            function ($item) {
                // Parameter #2 $multiplier of function str_repeat expects int, float|int given.
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

    public function getRules(array $params = []): array {
        return $this->rules($params);
    }

    public function rules(array $params = []): array {
        $act = '';
        extract($params);
        if ('' === $act) {
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
        if ('' !== $act) {
            $fields = collect($fields)->filter(
                function ($item) use ($act) {
                    if (! isset($item->except)) {
                        $item->except = [];
                    }

                    return ! \in_array($act, $item->except, true);
                }
            )->all();
        }

        foreach ($fields as $field) {
            if (\in_array($field->type, ['Cell', 'CellLabel'], true)) {
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
                /*
                if (Str::contains($item->type, 'RelationshipOne')) {
                    $rows_tmp = $this->row->{$item->name}();
                    if (method_exists($rows_tmp, 'getLocalKeyName')) {
                        $name1 = $rows_tmp->getLocalKeyName();
                    } else {
                        $name1 = $rows_tmp->getForeignKeyName();
                    }
                    $item->name = $name1;
                }
                */
                if ('pivot_rules' === $item->rules) {
                    $rel_name = $item->name;
                    $pivot_class = with(new $this::$model())
                        ->$rel_name()
                        ->getPivotClass();
                    // $pivot = new $pivot_class();
                    $pivot = app($pivot_class());
                    $pivot_panel_name = StubService::make()->setModelAndName($pivot, 'panel')->get();
                    $pivot_panel = app($pivot_panel_name);
                    $pivot_panel->setRows(with(new $this::$model())->$rel_name());
                    /**
                     * @var array
                     */
                    $pivot_panel_rules=$pivot_panel->rules();
                    $pivot_rules = collect($pivot_panel_rules)
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

    public function rulesMessages(): array {
        $lang = app()->getLocale();

        $fields = collect($this->fields())
            ->map(
                function ($item) {
                    return (new FieldService())->setVars(get_object_vars($item));
                }
            );

        $rules_msg_fields = $fields->filter(
            function ($value, $key) use ($lang) {
                return isset($value->rules_messages) && isset($value->rules_messages[$lang]);
            }
        )
            ->map(
                function ($item) use ($lang) {
                    $tmp = [];

                    foreach ($item->rules_messages[$lang] as $k => $v) {
                        $tmp[$item->name.'.'.$k] = $v;
                    }

                    return $tmp;
                }
            )
            ->collapse()
            ->all();
        $mod = Str::before(Str::after(static::$model, 'Modules\\'), '\\');
        $mod = strtolower($mod);
        $name = Str::snake(class_basename(static::$model));
        $trans_ns = $mod.'::'.$name.'__rules_messages';
        // dddx($trans_ns);//food::restaurant_owner__rules_messages
        $rules_msg = trans($trans_ns);
        if (! \is_array($rules_msg)) {
            $rules_msg = [];
        }
        $rules_msg_generic = trans('theme::generic');
        if (! \is_array($rules_msg_generic)) {
            $rules_msg_generic = [];
        }
        $msg = [];
        // $msg = \array_merge($msg,$rules_msg_generic);
        // $msg = \array_merge($msg, $rules_msg);
        $msg = array_merge($msg, $rules_msg_fields);

        return $msg;
    }

    /**
     * Get the filters available for the resource.
     * Interagisce con la funzione applyFilter.
     */
    public function filters(Request $request = null): array {
        return [];
        /* esempio di filters da mettere nel pannello interessato
        return [
            (object) [
                'param_name' => 'stabi', //nome dell'input
                'field_name' => 'stabi', //nome del campo collegato
                'rules' => 'required',
                //'op'=>'=',
            ],
            (object) [
                'param_name' => 'repar',
                'field_name' => 'repar',
                'rules' => 'required',
                //'op'=>'=',
            ],
            (object) [
                'param_name' => 'year',
                'field_name' => 'anno',
                'rules' => 'required',
                //'where_method' => 'ofYear',
                //'op'=>'=',
            ],
        ];
        */
    }

    public function getXotModelName(): ?string {
        /**
         * @var array
         */
        $models=config('morph_map');
        $res = collect($models)->search(static::$model);
        if (!is_string($res)) {
            return null;
        }

        return $res;
    }

    /**
     * index Navigation view.
     * view utilizzata nell'index dei modelli (model/index) e nell'admin.
     *
     * @return null
     */
    public function indexNav(): ?Renderable {
        return null;
    }

    /**
     * Undocumented function.
     *
     * @return Collection<PanelContract>
     */
    public function getActions(array $params = []) {
        return (new PanelActionService($this))->{__FUNCTION__}($params);
    }

    public function containerActions(array $params = []): Collection {
        return (new PanelActionService($this))->{__FUNCTION__}($params);
    }

    /**
     * Undocumented function.
     *
     * @return Collection<XotBasePanelAction>
     */
    public function itemActions(array $params = []): Collection {
        return (new PanelActionService($this))->{__FUNCTION__}($params);
    }

    public function getAction(string $name): XotBasePanelAction {
        return (new PanelActionService($this))->{__FUNCTION__}($name);
    }

    /**
     * crea l'oggetto del pannello item (quello dove passi $row).
     */
    public function itemAction(string $act): XotBasePanelAction {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    /**
     * crea l'oggetto del pannello Container (quello dove passi $rowS).
     */
    public function containerAction(string $act): XotBasePanelAction {
        return (new PanelActionService($this))->{__FUNCTION__}($act);
    }

    /**
     * @return mixed
     */
    public function urlContainerAction(string $act, array $params = []) {
        return (new PanelActionService($this))->{__FUNCTION__}($act, $params);
    }

    /**
     * @return mixed
     */
    public function urlItemAction(string $act, array $params = []) {
        return (new PanelActionService($this))->{__FUNCTION__}($act, $params);
    }

    /**
     * @return mixed
     */
    public function btnItemAction(string $act, array $params = []) {
        return (new PanelActionService($this))->{__FUNCTION__}($act, $params);
    }

    // -- nella registrazione 1 tasto, nelle modifiche 3
    // public function btnSubmit() {
    // return Form::bsSubmit('save');
    //    return Form::bsSubmit(trans('xot::buttons.save'));
    // }

    /**
     * Build an "index" query for the given resource.
     * funzione richiamata prima di rilasciare rows nella blade.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public static function indexQuery(array $data, $query) {
        // return $query->where('user_id', $request->user()->id);
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
    public static function relatableQuery(Request $request, $query) {
        // return $query->where('user_id', $request->user()->id);
        // return $query->where('user_id', $request->user()->id);
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

    // |\Illuminate\Database\Query\Builder

    /**
     * Undocumented function.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public function applyFilter($query, array $filters) {
        return RowsService::filter($query, $filters, $this->filters());
    }

    /**
     * https://lyften.com/projects/laravel-repository/doc/searching.html.
     * https://spatie.be/docs/laravel-query-builder/v3/features/filtering
     * https://github.com/spatie/laravel-query-builder/issues/452.
     * https://forum.laravel-livewire.com/t/anybody-using-spatie-laravel-query-builder-with-livewire/299/5
     * https://github.com/spatie/laravel-query-builder/issues/243.
     * https://github.com/spatie/laravel-query-builder/pull/223.
     *
     * @param mixed $query
     */

    /**
     * Undocumented function.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public function applySearch($query, ?string $q) {
        return RowsService::search($query, $q, $this->search());
    }

    /**
     * Undocumented function.
     *
     * @param RowsContract $query
     *
     * @return RowsContract
     */
    public function applySort($query, ?array $sort) {
        if (! \is_array($sort)) {
            return $query;
        }
        // dddx([$query, $sort]);
        if (isset($sort['by'])) {
            $column = $sort['by'];
        } else {
            $column = '';
        }
        /*
        * valutare se mettere controllo se colonna e' sortable
        **/
        if ('' === $column) {
            return $query;
        }
        $direction = isset($sort['order']) ? $sort['order'] : 'asc';
        $tmp = explode('|', $column);
        if (\count($tmp) > 1) {
            $column = $tmp[0];
            $direction = $tmp[1];
        }
        $query = $query->orderBy($column, $direction);

        return $query;
    }

    /**
     * @return mixed
     */
    public function formCreate(array $params = []) {
        return $this->form->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function formEdit(array $params = []) {
        return $this->form->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function formLivewireEdit(array $params = []) {
        return $this->form->{__FUNCTION__}($params);
    }

    /**
     * @return mixed
     */
    public function getFormData(array $params = []) {
        return $this->form->{__FUNCTION__}($params);
    }

    public function editObjFields(): array {
        return $this->form->{__FUNCTION__}();
    }

    public function getFields(array $params = []): Collection {
        return $this->form->{__FUNCTION__}($params);
    }

    public function btnHtml(array $params): string {
        return $this->form->{__FUNCTION__}($params);
    }

    public function btnCrud(array $params = []): string {
        // return $this->form->{__FUNCTION__}($params);
        return (new PanelFormService($this))->{__FUNCTION__}();
    }

    public function imageHtml(array $params): string {
        // usare PanelImageService
        /*
        * mettere imageservice, o quello di spatie ?
        *
        **/
        if (! property_exists($this->row, 'image_src')) {
            throw new \Exception('in ['.\get_class($this->row).'] property [image_src] is missing');
        }
        $params['src'] = $this->row->image_src;
        $img = ImageService::make()->setVars($params);
        $src = $img->fit()->save()->src();
        if (! \is_string($src)) {
            throw new \Exception('src is not a string');
        }

        return '<img src="'.asset($src).'" >';
    }

    public function imgSrc(array $params): string {
        $params['dirname'] = '/photos/'.$this->postType().'/'.$this->guid();
        $params['src'] = optional($this->row)->image_src;
        $img = ImageService::make()->setVars($params);

        return $img->url();
    }

    public function microdataSchemaOrg(): string {
        return '';
    }

    public function show_ldJson(): array {
        return [];
    }

    public function relatedUrl(string $name, string $act = 'index'): string {
        return $this->route->{__FUNCTION__}($name, $act);
    }

    public function langUrl(string $lang): string {
        return $this->route->{__FUNCTION__}(['lang' => $lang]);
    }

    public function url(string $act = 'show'): string {
        return $this->route->{__FUNCTION__}($act);
    }

    public function relatedName(string $name, ?int $id = null): PanelContract {
        // -- il name e' il nome della relazione ..
        /*
        $model = xotModel($name);
        if (null != $id) {
            $model = $model->find($id);
        }
        $panel = PanelService::make()->get($model);
        $panel = $panel->setParent($this);

        return $panel;
        */

        $name = Str::camel($name); // nome relazioni sono per convenzione in camel case
        $related = $this->row->{$name}()->getRelated();
        $relatedPanel = PanelService::make()->get($related);
        $relatedPanel->setName($name);
        $relatedPanel->setParent($this);

        return $relatedPanel;
    }

    public function postType(): string {
        /**
         * @var array
         */
        $models=config('morph_map');
        $post_type = collect($models)->search(\get_class($this->row));
        if (false === $post_type) {
            $post_type = snake_case(class_basename($this->row));
        }

        return (string) $post_type;
    }

    /**
     * Undocumented function.
     */
    public function guid(?bool $is_admin = null): ?string {
        if (isset($is_admin) && $is_admin) {
            return (string) $this->row->getKey();
        }/*
        if (null !== $this->getInAdmin() && $this->getInAdmin()) {
            return (string) $this->row->getKey();
        }
        */
        if (inAdmin()) {
            return (string) $this->row->getKey();
        }
        $row = $this->row;
        $key = $row->getRouteKeyName();
        // if (! in_array($key, $row->getFillable())) {
        //    throw new \Exception('field ['.$key.'] not set in model ['.class_basename($row).'] fillable ');
        // }

        if (null === $row->getKey()) {
            return null;
        }
        // 1049   Dead catch - Exception is never thrown in the try block.
        // try {
        $guid = $row->$key;
        // } catch (\Exception $e) {
        //     $guid = '';
        // }
        if ('' === $guid && method_exists($row, 'post') && 'guid' === $key && property_exists($row, 'post')) {
            // if ('' == $row->id && '' != $row->post_id) {
            //    $row->id = $row->post_id; //finche netson non riabilita migrazioni
            // }
            // 1059   Dead catch - Exception is never thrown in the try block.
            // try {
            return $row->post->guid;
            // } catch (\Exception $e) {
            //                $title = $this->postType().' '.$this->row->getKey();

            //    $post = $row->post()->firstOrCreate(
            //        [
            //            'lang' => app()->getLocale(),
            //        ],
            //        [
            //            'title' => $title,
            //            'guid' => Str::slug($title),
            //        ]
            //    );

            //    return $post->guid;
            // }
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

    public function getCrudActions(): array {
        $acts = ['index', 'create'];
        /* indexedit in via di deprecazione
        if (
            //1095   Cannot call method getName() on object|string.

            Str::endsWith(
                request()
                    ->route()
                    ->getName(),
                'index_edit',
            )
        ) {
            $acts = ['indexEdit', 'create'];
        }
        */
        /*
        $route_params = getRouteParameters();
        $panel = PanelService::make()->getByParams($route_params);
        */
        // dddx(PanelService::make()->getRequestPanel()->row->getKey());
        // dddx($this->row->getKey());
        if ($this->row->getKey()) {
            $acts[] = 'edit';
        }

        $trad_mod = $this->getTradMod();
        $actions = [];
        foreach ($acts as $act) {
            $url = $this->url($act);
            $url1 = Str::before($url, '?');
            $req_path = '/'.request()->path();
            $active = $url1 === $req_path;

            $tmp = new \stdClass();
            $tmp->title = trans($trad_mod.'.tab.'.$act);
            $tmp->url = $url;
            $tmp->act = $act;
            $tmp->active = $active;

            if (Gate::allows($act, $this)) {
                $actions[] = $tmp;
            }
        }

        return $actions;
    }

    public function getTradMod(): string {
        $mod_low = $this->getModulenameLow();
        $str = $mod_low.'::'.Str::snake($this->getName());

        return $str;
    }

    public function getItemTabs(): array {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    public function getRowTabs(): array {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    public function getTabs(): array {
        return (new PanelTabService($this))->{__FUNCTION__}();
    }

    // Return value of Modules\Xot\Models\Panels\XotBasePanelService::rows()
    // must be an instance of Illuminate\Database\Eloquent\Builder,
    // instance of Illuminate\Database\Eloquent\Relations\MorphToMany returned

    /*
    public function rowsTest(?array $data = null) {
        $query = $this->getRows();
        $test = app(Pipeline::class)
        ->send($query)
        ->through([
            //'App\QueryFilters\LikeMatch:search,title,excerpt,summary',
            'Modules\Xot\QueryFilters\Search',
        ])
        ->thenReturn()
        ->get();
        dddx(['query' => $query, 'test' => $test]);
    }
    */

    /**
     * init + filter + search + sort => ??? trovare nome.
     *
     * @return RowsContract
     */
    public function rows(?array $data = null) {
        if (null === $data) {
            $data = request()->all();
        }

        $filters = $data;
        $q = isset($data['q']) ? $data['q'] : null;
        $sort = isset($data['sort']) ? $data['sort'] : null;
        $query = $this->getRows();
        // $query = $this->getBuilder();

        $with = $this->with();
        if (method_exists($query, 'with')) {
            $query = $query->with($with);
        }
        // https://laravelvuejs.com/query-filters-in-laravel-70cafa5d4b64
        /*
        $test=app(Pipeline::class)
            ->send($query)
            ->through([
                'App\QueryFilters\LikeMatch:search,title,excerpt,summary',
            ])
            ->thenReturn()
            ->paginate();
        */

        $query = $this->indexQuery($data, $query);
        $query = $this->applyFilter($query, $filters);
        $query = $this->applySearch($query, $q);

        if (! Route::is('*edit*')) {
            $query = $this->applySort($query, $sort);
        }

        /*
        $page = isset($data['page']) ? $data['page'] : 1;
        Cach1e::forever('page', $page);
        */

        return $query;
    }

    public function getFillable(): array {
        return $this->row->getFillable();
    }

    /**
     * @return mixed
     */
    public function callItemActionWithGate(string $act) {
        // $actions = $this->actions();
        // dddx([get_class($this), $actions]);
        $method_act = Str::camel($act);
        $authorized = Gate::allows($method_act, $this);

        if (! $authorized) {
            return $this->notAuthorized($method_act);
        }

        return $this->callItemAction($act);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function notAuthorized(string $method) {
        $policy_class = PolicyService::get($this)->createIfNotExists()->getClass();

        $lang = app()->getLocale();
        if (! \Auth::check()) {
            $referer = \Request::path();

            return redirect()->route('login', ['lang' => $lang, 'referer' => $referer])
                ->withErrors(['active' => 'login before']);
        }

        $msg = 'Auth Id ['.\Auth::id().'] not can ['.$method.'] on ['.$policy_class.']';

        if (! view()->exists('pub_theme::errors.403')) {
            $msg = '<h3> Aggiungere la view : pub_theme::errors.403<br/>pub_theme: '.config('xra.pub_theme').'</h3>';
            throw new \Exception($msg);
        }

        return response()->view('pub_theme::errors.403', ['message' => $msg], 403);
    }

    /**
     * @return mixed
     */
    public function callAction(string $act) {
        // $act = Str::camel($act);

        // $action = $this->getActions()
        //    ->firstWhere('name', $act);
        $action = $this->getAction($act);

        if (! \is_object($action)) {
            $msg = 'action '.$act.' not recognized for ['.static::class.']';
            FileService::viewCopy('theme::errors.403', 'pub_theme::errors.403');

            return response()->view('pub_theme::errors.403', ['message' => $msg], 403);
        }

        $action->setRow($this->row);
        $rows = $this->rows();
        $action->setRows($rows);

        $action->setPanel($this);

        $method = request()->getMethod();
        if ('GET' === $method) {
            return $action->handle();
        } else {
            return $action->postHandle();
        }
    }

    /**
     * @return mixed
     */
    public function callItemAction(string $act) {
        // Strict comparison using === between null and string will always evaluate to false
        // if (null === $act) {
        //    return null;
        // }
        // $action = $this->itemActions()
        //    ->firstWhere('name', $act);
        $action = $this->itemAction($act);

        if (! \is_object($action)) {
            $msg = '<h3>['.$act.'] not exists in ['.static::class.']</h3>Items Actions Avaible are :';
            foreach ($this->itemActions() as $act) {
                $msg .= '<br/>'.$act->getName();
            }

            return $msg;
        }
        $action->setRow($this->row);
        $action->setPanel($this);
        $method = request()->getMethod();
        if ('GET' === $method) {
            $out = $action->handle();
        } else {
            $out = $action->postHandle();
        }

        return $out;
    }

    /**
     * @return mixed
     */
    public function callContainerAction(string $act) {
        // Strict comparison using === between null and string will always evaluate to false
        // if (null === $act) {
        //    return null;
        // }
        // $action = $this->containerActions()
        //    ->firstWhere('name', $act);
        $action = $this->containerAction($act);

        if (! \is_object($action)) {
            abort(403, 'action '.$act.' not recognized');
        }

        $data = request()->all();
        $rows = $this->rows($data);
        $action->setRows($rows);
        $action->setPanel($this);
        $method = request()->getMethod();
        if ('GET' === $method) {
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
        // dddx($this->presenter);//Modules\Xot\Presenters\HtmlPanelPresenter
        try {
            return $this->presenter->out();
        } catch (\Exception $e) {
            /**
             * @phpstan-var view-string
             */
            $view = 'pub_theme::errors.500';
            if (! view()->exists($view)) {
                FileService::viewCopy('theme::errors.500', 'pub_theme::errors.500');
            }

            return response()->view($view, ['message' => $e->getMessage()], 500);
        }
    }

    /*//--- valutare
    public function __toString() {
        return $this->presenter->out()->render();
    }
    */

    public function pdfFilename(array $params = []): string {
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
    public function xls(array $params = []) {
        $presenter = new XlsPanelPresenter();
        $presenter->setPanel($this);

        return $presenter->out($params);
    }

    /**
     * Undocumented function.
     */
    public function pdf(array $params = []): string {
        $presenter = new PdfPanelPresenter();
        $presenter->setPanel($this);

        $presenter->setViewParams($params['view_params'] ?? []);

        return $presenter->out($params);
    }

    // Method Modules\Xot\Models\Panels\XotBasePanelService::related() should return Modules\Xot\Models\Panels\XotBasePanel but returns Modules\Xot\Contracts\PanelContract|null.
    public function related(string $relationship): PanelContract {
        $related = $this->row->$relationship()->getRelated();
        $panel_related = PanelService::make()->get($related);
        $panel_related->setParent($this);

        return $panel_related;
    }

    public function getModuleName(): string {
        $str = static::class;
        // $str = $this::$model;
        $module_name = Str::before(Str::after($str, 'Modules\\'), '\\Models\\');

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
            $tmp->url = $parent->url('index');
            $tmp->title = $parent->postType();
            $tmp->obj = \Theme::xotModel($tmp->title);
            $tmp->method = 'index';
            $bread[] = $tmp;
            try {
                $tmp = (object) [];
                $tmp->url = $parent->url('show');
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
    public function getExcerpt($length = 225) {
        $row = $this->row;
        /*
        if (! property_exists($row, 'subtitle')) {
            throw new \Exception('in ['.get_class($row).'] property [subtitle] is missing');
        }
        if (! property_exists($row, 'txt')) {
            throw new \Exception('in ['.get_class($row).'] property [txt] is missing');
        }
        //*/

        if ($row->getAttributeValue('subtitle')) {
            $content = $row->getAttributeValue('subtitle');
        } else {
            $content = $row->getAttributeValue('txt');
        }

        // [2022-05-20 00:22:19] local.ERROR: preg_replace():
        // Argument #3 ($subject) must be of type array|string, null given (View: /home/cvfcmxwn/laraxot/multi/laravel/Themes/DirectoryBs4/Resources/views/layouts/widgets/blog_items.blade.php) {"view":{"view":"/home/cvfcmxwn/laraxot/multi/laravel/Modules/Xot/Models/Panels/XotBasePanel.php","data":[]},"
        // url":"http://prosecco-valdobbiadene.it/?page=9","
        if (is_null($content)) {
            $content = '';
        }

        // 1737   Parameter #1 $str of function strip_tags expects string, array|string|null given.
        $tmp = preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content);
        if (\is_array($tmp)) {
            $tmp = implode(' ', $tmp);
        }
        if (null === $tmp) {
            $tmp = '';
        }
        $cleaned = strip_tags($tmp, '<code>');
        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return \strlen($cleaned) > $length
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
     * under costruction,.
     */
    public function getViews(): array {
        $views = [];
        $act = RouteService::getAct();
        $view = $this->getModuleNameLow().'::'.(inAdmin() ? 'admin.' : '').$this->getName().'.'.$act;
        $views[] = $view;
        $view = (inAdmin() ? 'adm_theme' : 'pub_theme').'::layouts.default.'.$act;
        $views[] = $view;
        /**
         * @phpstan-var view-string
         */
        $view = 'theme::layouts.default'.(inAdmin() ? '.admin' : '').'.'.$act;
        $views[] = $view;

        return $views;
    }

    public function id(?bool $is_admin = null): string {
        $curr = $this;
        $data = collect([]);
        while (null !== $curr) {
            // $data->prepend($curr->postType().'-'.$curr->guid($is_admin));
            $data->prepend($curr->postType().'-'.$curr->getRow()->getKey());

            $curr = $curr->getParent();
        }

        return $data->implode('-');
    }

    /**
     * Get the tabs available.
     */
    public function tabs(): array {
        return [];
    }

    /**
     * Get the actions available for the resouce.
     */
    public function actions(): array {
        return [];
    }

    /**
     * @return mixed
     */
    public function update(array $data) {
        // $func = '\Modules\Xot\Jobs\Crud\\'.Str::studly(__FUNCTION__).'Job';
        $func = '\Modules\Xot\Jobs\PanelCrud\\'.Str::studly(__FUNCTION__).'Job';

        $panel = $func::dispatchNow($data, $this);

        return $panel;
    }

    public function isRevisionBy(UserContract $user): bool {
        $post = $this->getRow();
        if ($post->getAttributeValue('created_by') === $user->handle
            || $post->getAttributeValue('updated_by') === $user->handle
            || $post->getAttributeValue('user_id') === $user->id
        ) {
            return true;
        }

        return false;
    }

    public function isAuthoredBy(UserContract $user): bool {
        /**
         * @var ModelWithAuthorContract
         */
        $row = $this->getRow();
        if (null == $row->author) {
            return false;
        }
        // return $row->author->is($user);
        return $row->author_id == $user->id;
    }

    /**
     * ----------------------- WIP -----------------------.
     */
    public function isModeratedBy(UserContract $user): bool {
        /**
         * @var ModelWithAuthorContract
         */
        $row = $this->getRow();
        if (null == $row->author) {
            return false;
        }

        // return $row->author->is($user);
        return $row->author_id == $user->id;
    }

    /**
     * ----------------------- WIP -----------------------.
     */
    public function isAdminedBy(UserContract $user): bool {
        /**
         * @var ModelWithAuthorContract
         */
        $row = $this->getRow();
        if (null == $row->author) {
            return false;
        }

        // return $row->author->is($user);
        return $row->author_id == $user->id;
    }
}
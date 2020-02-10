<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Modules\Xot\Services\PanelService as Panel;
use Route;

class RouteService {
    protected static $namespace_start = '';
    protected static $curr = null;

    public static function getGroupOpts($v, $namespace) {
        $group_opts = [
            'prefix' => self::getPrefix($v, $namespace),
            'namespace' => self::getNamespace($v, $namespace),
            'as' => self::getAs($v, $namespace),
        ];

        return $group_opts;
    }

    public static function getPrefix($v, $namespace) {
        if (\in_array('prefix', \array_keys($v), true)) {
            return $v['prefix'];
        }
        $prefix = \mb_strtolower($v['name']);
        ///*
        $param_name = self::getParamName($v, $namespace);
        if ('' != $param_name) {
            if (\is_array($param_name)) {
                return $prefix.'/{'.\implode('}/{', $param_name).'}';
            }

            return $prefix.'/{'.$param_name.'}';
        }
        //*/
        /*
        $params_name=self::getParamsName($v,$namespace);
        if($params_name!=[]){
        return $prefix.'/{'.implode('}/{',$params_name).'}';
        }
         */
        return $prefix;
    }

    public static function getAs($v, $namespace) {
        if (\in_array('as', \array_keys($v), true)) {
            return $v['as'];
        }
        $as = \mb_strtolower($v['name']).'';
        $as = \str_replace('/', '.', $as);
        $as = \preg_replace('/{.*}./', '', $as);

        $as = \str_replace('{', '', $as);
        $as = \str_replace('}', '', $as);

        return $as.'.';
    }

    public static function getNamespace($v, $namespace) {
        if (\in_array('namespace', \array_keys($v), true)) {
            return $v['namespace'];
        }
        //if($namespace!=null){
        $namespace = $v['name'];
        //}
        $namespace = \str_replace('{', '', $namespace);
        $namespace = \str_replace('}', '', $namespace);
        if ('' == $namespace) {
            return null;
        }

        return Str::studly($namespace);
    }

    public static function getAct($v, $namespace) {
        if (\in_array('act', \array_keys($v), true)) {
            return $v['act'];
        }
        $v['act'] = $v['name'];
        $v['act'] = \preg_replace('/{.*}\//', '', $v['act']);
        $v['act'] = \str_replace('/', '_', $v['act']);
        $v['act'] = Str::camel($v['act']);
        $v['act'] = \str_replace('{', '', $v['act']);
        $v['act'] = \str_replace('}', '', $v['act']);
        //camel_case foo_bar  => fooBar
        //studly_case foo_bar => FooBar
        return Str::camel($v['act']);
    }

    public static function getParamName($v, $namespace) {
        if (\in_array('param_name', \array_keys($v), true)) {
            return $v['param_name'];
        }
        $param_name = 'id_'.$v['name'];
        $param_name = \str_replace('{', '', $param_name);
        $param_name = \str_replace('}', '', $param_name);
        //$param_name=null;
        $param_name = \mb_strtolower($param_name);

        return $param_name;
    }

    public static function getParamsName($v, $namespace) {
        $param_name = self::getParamName($v, $namespace);
        if (! \is_array($param_name)) {
            $params_name = [$param_name];
        } else {
            $params_name = $param_name;
        }

        return $params_name;
    }

    public static function getResourceOpts($v, $namespace) {
        $param_name = self::getParamName($v, $namespace);
        $params_name = self::getParamsName($v, $namespace);
        $opts = [
            'parameters' => [\mb_strtolower($v['name']) => \implode('}/{', $params_name)],
            'names' => self::prefixedResourceNames(self::getAs($v, $namespace)),
        ];
        if (isset($v['only'])) {
            $opts['only'] = $v['only'];
        }
        if ('' == $param_name && ! isset($opts['only'])) {
            $opts['only'] = ['index'];
        }
        $where = [];
        foreach ($params_name as $pn) {
            $where[$pn] = '[0-9]+';
        }
        $opts['where'] = $where; //se c'e' "id_" di sicuro e' un numero
        return $opts;
    }

    public static function getController($v, $namespace) {
        if (\in_array('controller', \array_keys($v), true)) {
            return $v['controller'];
        }
        $v['controller'] = $v['name'];
        $v['controller'] = \str_replace('/', '_', $v['controller']);
        $v['controller'] = \str_replace('{', '', $v['controller']);
        $v['controller'] = \str_replace('}', '', $v['controller']);
        $v['controller'] = Str::studly($v['controller']);
        //camel_case foo_bar  => fooBar
        //studly_case foo_bar => FooBar
        $v['controller'] = $v['controller'].'Controller';

        return $v['controller'];
    }

    public static function getUri($v, $namespace) {
        $uri = \mb_strtolower($v['name']);
        /*
        $v['prefix']=self::getPrefix($v,$namespace);
        if(isset($v['prefix'])){ //------------ !!!!! da verificare che non faccia danni
        $uri=$v['prefix'].'/'.$uri;
        }
         */
        return $uri;
    }

    public static function getMethod($v, $namespace) {
        if (\in_array('method', \array_keys($v), true)) {
            return $v['method'];
        }

        return ['get', 'post'];
    }

    public static function getUses($v, $namespace) {
        $controller = self::getController($v, $namespace);
        $act = self::getAct($v, $namespace);
        $uses = $controller.'@'.$act;

        return $uses;
    }

    public static function getCallback($v, $namespace, $curr) {
        $as = Str::slug($v['name']); //!!!!!! test da controllare
        $uses = self::getUses($v, $namespace);
        if (null != $curr) {
            $uses = '\\'.self::$namespace_start.'\\'.$curr.'\\'.$uses;
        } else {
            $uses = '\\'.self::$namespace_start.'\\'.$uses;
        }
        $callback = ['as' => $as, 'uses' => $uses];

        return $callback;
    }

    public static function dynamic_route($array, $namespace = null, $namespace_start = null, $curr = null) {
        if (null != $namespace_start) {
            self::$namespace_start = $namespace_start;
        } /*
        if($curr!=null){
        static::$curr=$curr;
        }*/
        \reset($array);
        foreach ($array as $k => $v) {
            $group_opts = self::getGroupOpts($v, $namespace);
            $v['group_opts'] = $group_opts;
            self::createRouteResource($v, $namespace);
            Route::group($group_opts, function () use ($v, $namespace, $curr) {
                self::createRouteActs($v, $namespace, $curr);
                self::createRouteSubs($v, $namespace, $curr);
            });
        } //end foreach
    }

    //end function

    //--------------------------------------------------------------------------------
    public static function createRouteResource($v, $namespace) {
        if (null == $v['name']) {
            return;
        }
        $opts = self::getResourceOpts($v, $namespace);
        $controller = self::getController($v, $namespace);
        $name = \mb_strtolower($v['name']);
        Route::resource($name, $controller, $opts)
        //->where(['container1' => "^((?!create|edit).)*$"])  //BadMethodCallException Method Illuminate\Routing\PendingResourceRegistration::where does not exist.
        //  ->middleware('manageContainer','container1')
        ; //->where(['id_'.$v['name'] => '[0-9]+']);
    }

    // ------------------------------------------------------------------------------
    public static function createRouteSubs($v, $namespace, $curr) {
        if (! isset($v['subs'])) {
            return;
        }
        $sub_namespace = self::getNamespace($v, $namespace);
        /*
        if(self::$curr==null){
        self::$curr=$sub_namespace;
        }else{
        if(self::$curr!=$sub_namespace){
        self::$curr=self::$curr.'\\'.$sub_namespace;
        }
        }
         */
        if (null == $curr) {
            $curr = $sub_namespace;
        } else {
            $piece = \explode('\\', $curr);
            if (last($piece) != $sub_namespace && $curr != $sub_namespace) {
                $curr .= '\\'.$sub_namespace;
            }
        }

        self::dynamic_route($v['subs'], $sub_namespace, null, $curr);
    }

    //---------------------------------------------------
    public static function createRouteActs($v, $namespace, $curr) {
        if (! isset($v['acts'])) {
            return;
        }
        \reset($v['acts']);

        $controller = self::getController($v, $namespace);
        foreach ($v['acts'] as $k1 => $v1) {
            $v1['controller'] = $controller; //le acts hanno il controller del padre
            $method = self::getMethod($v1, $namespace);
            $uri = self::getUri($v1, $namespace);
            $callback = self::getCallback($v1, $namespace, $curr);
            if (\is_array($method)) {
                Route::match($method, $uri, $callback);
            } else {
                Route::$method($uri, $callback);
            }
        } //endforeach
    }

    // /--------------------------------------------------------

    public static function routes() {
        if ('' != \Request::path()) {
            $tmp = \explode('/', \Request::path());
            $tmp = \array_slice($tmp, 0, 2);
            $tmp = \implode('_', $tmp);
            //echo '<h3>tmp = '.$tmp.'</h3>';die();
            $filename = 'web_'.$tmp.'.php';

            $tmp = \debug_backtrace();
            dd($tmp[3]['class']);

            $filename_dir = __DIR__.\DIRECTORY_SEPARATOR.$filename;
            echo '<h3>tmp = '.$filename_dir.'</h3>';
            die();
            if (\file_exists($filename_dir)) {
                require $filename_dir;
            }
        }
    }

    //end routes
    //------------------------------------------------------------------
    public static function prefixedResourceNames($prefix) {
        if ('.' == \mb_substr($prefix, -1)) {
            $prefix = \mb_substr($prefix, 0, -1);
        }
        if ('' == $prefix || null == $prefix) {
            return ['index' => $prefix.'index', 'create' => $prefix.'create', 'store' => 'store', 'show' => $prefix.'show', 'edit' => $prefix.'edit', 'update' => $prefix.'update', 'destroy' => $prefix.'destroy'];
        }
        $prefix = \mb_strtolower($prefix);

        return ['index' => $prefix.'.index', 'create' => $prefix.'.create', 'store' => $prefix.'.store', 'show' => $prefix.'.show', 'edit' => $prefix.'.edit', 'update' => $prefix.'.update', 'destroy' => $prefix.'.destroy'];
    }

    //end prefixedResourceNames

    public static function containerN($params) {
        extract($params);
        if (! isset($model)) {
            ddd($params);
        }
        $name = collect(config('xra.model'))->search($model);
        if (! isset($route_params)) {
            $route_current = \Route::current();
            $route_params = is_object($route_current) ? $route_current->parameters() : [];
        }
        list($containers, $items) = params2ContainerItem($route_params);
        $container_types = [];
        foreach ($containers as $i => $container) {
            $types = Str::camel(Str::plural($container));
            if (0 == $i) {
                $container_types[$i] = config('xra.model.'.$container);
            } else {
                $item_prev = $items[$i - 1];
                if (is_string($item_prev)) {
                    $container_prev = $container_types[$i - 1];
                    $container_prev_obj = new $container_prev();
                    $container_prev_panel = Panel::get($container_prev_obj);
                    $other_lang = \Modules\Blog\Models\Post::where('post_type', $container_prev_panel->postType())
                        ->where('guid', $item_prev)
                        ->first();
                    if (is_object($other_lang)) {
                        $up = $other_lang->replicate();
                        $up->lang = \App::getLocale();
                        $up->save();
                        $item_prev = $container_prev::firstOrCreate(['post_id' => $up->post_id]);
                    }
                }
                if (! is_object($item_prev)) {
                    abort(404);
                }
                $rows = $item_prev->$types();
                $container_types[$i] = get_class($rows->getRelated());
            }
        }
        $container_i = collect($container_types)->search($model);
        /*
        ddd($container_types);
        //$item_i=collect($items)->whereInstanceOf($model);
        $item_i=collect($items)->search(function ($item,$key) use ($model){
        return get_class($item)==$model;
        });

        $item_i=$item_i*1;
        if($item_i>0){
        return $item_i;
        }

        $container_i = collect($containers)->search($name);
         */
        return $container_i * 1;
    }

    /*
    public static function routenameN($params) {
    $routename = \Route::currentRouteName();
    $act = last(explode('.', $routename));
    extract($params);
    $tmp = [];
    if (in_admin()) {
    $tmp[] = 'admin';
    }
    for ($i = 0; $i <= $n + 1; ++$i) {
    ) $tmp[] = 'container'.$i;
    }
    $tmp[] = $act;
    $routename_n = implode('.', $tmp);
    return $routename_n;
    }
     */
    public static function routenameSon($params) {
        $container_i = self::containerN($params);
        $routename = \Route::currentRouteName();
        extract($params);
        $act = last(explode('.', $routename));
        if (in_array($act, ['edit', 'index_edit'])) {
            $act = 'index_edit';
        } else {
            $act = 'index';
        }
        //*
        $tmp = [];
        if (in_admin()) {
            $tmp[] = 'admin';
        }
        for ($i = 0; $i <= $container_i + 1; ++$i) {
            $tmp[] = 'container'.$i;
        }
        $tmp[] = $act;
        $rountename_son = implode('.', $tmp);
        //*/
        //$rountename_son=in_admin()?'admin.':'';
        //$rountename_son.=str_repeat()
        //$rountename_son=str_repeat() //da fare
        return $rountename_son;
    }

    public static function urlSon($params, $son_name = '') {
        $container_i = self::containerN($params);
        $routename_son = self::routenameSon($params);
        extract($params);
        $params = \Route::current()->parameters();
        $parz = $params;
        $parz['container'.($container_i + 1)] = $son_name;
        try {
            $route = route($routename_son, $parz);
        } catch (\Exception $e) {
            $msg = [
                'container_i' => $container_i,
                'routename_son' => $routename_son,
                'son_name' => $son_name,
            ];
            //echo '<pre>'.print_r($msg,true).'</pre>';ddd($parz);
            $route = '#';
        }

        return $route;
    }

    public static function tabs($params) {
        extract($params);
        $route_params = \Route::current()->parameters();
        $routename = \Route::currentRouteName();
        $act = last(explode('.', $routename));
        list($containers, $items) = params2ContainerItem($route_params);
        $n_items = count($items);
        $item_last = last($items);
        if (0 == count($items)) {
            return [];
        }
        $tabs = [];

        $cont_i = RouteService::containerN(['model' => $model, 'route_params' => $route_params]);
        //ddd($routename);
        if (0 == $cont_i) {
            $tmp1 = new \stdClass();
            if ('index_edit' == $act) {
                $act = 'edit';
            }
            if ('index' == $act) {
                $act = 'show';
            }
            $tmp1->title = $act;
            $tmp1->routename = (in_admin() ? 'admin.' : '').'container0.'.$act;
            $tmp1->url = route($tmp1->routename, $route_params);
            $tmp1->active = ($routename == $tmp1->routename);
            $tabs[] = $tmp1;
        }
        //echo('[ '.$model.']['.$cont_i.']');

        if (isset($params['item'.$cont_i])) {
            foreach ($tabs_name as $k => $v) {
                $tmp1 = new \stdClass();
                $tmp1->title = $v;
                $tmp1->active = in_array($v, $containers);
                $tmp1->routename = RouteService::routenameSon(['model' => $model]);
                /*
                $path=collect(explode('.',$tmp1->routename))->map(function ($v,$k) use ($params) {
                if(isset($params[$v])) return $params[$v];
                return $v;
                })->implode('.');
                $trad=implode('.',array_slice(explode('.',$path),0,-1));
                $tmp1->trad='pub_theme::'.$trad;
                 */
                //$tmp1->title=trans($tmp1->trad.'.tab.'.$v);
                $tmp1->title = trans('pub_theme::'.Arr::first($containers).'.tab.'.$v);
                $tmp1->url = RouteService::urlSon(['model' => $model], $v);
                $tabs[] = $tmp1;
            }
        }
        $data[$cont_i] = $tabs;
        if (count($containers) > 1 && $cont_i > 0) {
            $panel = StubService::getByModel($items[$cont_i - 1], 'panel');
            $tabs_parent = $panel->tabs();
            $data = array_merge($tabs_parent, $data);
        }

        return $data;
    }

    public static function urlModel($params) {
        $lang = \App::getLocale();
        extract($params);
        $route_current = \Route::current();
        $route_params = is_object($route_current) ? $route_current->parameters() : [];
        //$routename = \Request::route()->getName();
        $cont_i = RouteService::containerN(['model' => get_class($model), 'route_params' => $route_params]);
        $parent = $model->parent;
        if (isset($panel_parent) && is_object($panel_parent)) {
            $parent = $panel_parent->row;
        }
        if (is_object($parent)) {
            $parent_cont_i = RouteService::containerN(['model' => get_class($parent), 'route_params' => $route_params]);
            $route_params['container'.($parent_cont_i)] = Panel::get($parent)->postType();
            $route_params['item'.($parent_cont_i)] = $parent;
            if ($parent_cont_i == $cont_i) {
                ++$cont_i;
            }
        }

        $tmp = [];
        if (in_admin()) {
            $tmp[] = 'admin';
        }
        for ($i = 0; $i <= $cont_i; ++$i) {
            $tmp[] = 'container'.$i;
        }
        $tmp[] = $act;
        $routename = implode('.', $tmp);
        $route_params['lang'] = $lang;
        $post_type = Panel::get($model)->postType();
        //if(!isset($route_params['container'.($cont_i)]) ){
        $route_params['container'.($cont_i)] = $post_type;
        //}

        $route_key = $model->getRouteKeyName();
        $route_key_val = $model->$route_key;
        if ('' == $route_key_val && '' != $model->getKey()) {
            ddd([
                'route_key' => $route_key,
                'class' => get_class($model),
                'primary_name' => $model->getKeyName(),
                'primary_value' => $model->getKey(),
            ]);
        }
        $route_params['item'.($cont_i)] = $route_key_val;
        try {
            $url = route($routename, $route_params);
        } catch (\Exception $e) {
            $url = '#routename_err['.__FILE__.']['.__LINE__.']';
        }
        if (Str::endsWith($url, '?')) {
            $url = Str::before($url, '?');
        }

        return $url;
    }

    public static function routenameN($params) {
        //--- default data
        $routename = \Route::currentRouteName();
        $act = last(explode('.', $routename));
        extract($params);
        $tmp = [];
        if (in_admin()) {
            $tmp[] = 'admin';
        }
        for ($i = 0; $i <= $n; ++$i) {
            $tmp[] = 'container'.$i;
        }
        $tmp[] = $act;
        $routename = implode('.', $tmp);

        return $routename;
    }

    /**
     * in = row , related.
     *
     **/
    public static function urlRelated($params) {
        extract($params);
        try {
            $params = \Route::current()->parameters();
        } catch (\Exception $e) {
            $params = [];
        }
        $cont_i = RouteService::containerN(['model' => get_class($row)]);
        $routename = RouteService::routenameN(['n' => $cont_i + 1, 'act' => $act]);
        $row_name = collect(config('xra.model'))->search(get_class($row));

        //$related_name=collect(config('xra.model'))->search(get_class($related));
        if (! isset($params['lang'])) {
            $params['lang'] = \App::getLocale();
        }

        $params['container'.$cont_i] = $row_name;
        $params['item'.$cont_i] = $row;
        $params['container'.($cont_i + 1)] = $related_name;
        try {
            $route = route($routename, $params, false);
        } catch (\Exception $e) {
            $route = '#['.__LINE__.']['.__FILE__.']';
        }

        return $route;
    }

    public static function urlAct($params) {
        $query = [];
        extract($params);
        $mutator = $act.'_url';
        try {
            $route = $row->$mutator;
        } catch (\Exception $e) {
            $route = '#';
        }
        $route_action = \Route::currentRouteAction();
        $old_act = Str::snake(Str::after($route_action, '@'));
        $routename = Request::route()->getName();
        $old_act_route = last(explode('.', $routename));

        $routename_act = Str::before($routename, $old_act_route).''.$act;
        try {
            $route_params = \Route::current()->parameters();
        } catch (\Exception $e) {
            $route_params = [];
        }
        if (\Route::has($routename_act)) {
            $parz = array_merge($route_params, [$row]);
            $parz = array_merge($parz, $query);
            $route = route($routename_act, $parz);
        } else {
            $route = '#'.$routename_act;
        }

        return $route;
    }
}

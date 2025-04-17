<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use function count;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Class RouteService.
 * Modules\Xot\Services\RouteService.
 *
 * @method string urlAct($params)
 */
class RouteService
{
    /**
     * @return array|bool|mixed
     */
    public static function inAdmin(array $params = [])
    {
        if (isset($params['in_admin'])) {
            return $params['in_admin'];
        }
        // dddx(ThemeService::__getStatic('in_admin'));
        /* Cannot call method get() on mixed
        if (null !== config()->get('in_admin')) {
            return config()->get('in_admin');
        }
        */
        if ('admin' === \Request::segment(1)) {
            return true;
        }
        $segments = \Request::segments();
        if (\count($segments) > 0 && 'livewire' === $segments[0]) {
            if (true === session('in_admin')) {
                return true;
            }
        }

        return false;
    }

    // --- sarebbe deprecata ma il mal di testa

    public static function urlAct(array $params): string
    {
        $query = [];
        $act = 'show';
        $row = (object) [];
        extract($params);
        /*
        $mutator = $act.'_url';
        try {
            $route = $row->$mutator;
        } catch (\Exception $e) {
            $route = '#';
        }
        */
        $route_action = (string) \Route::currentRouteAction();
        $old_act = Str::snake(Str::after($route_action, '@'));
        // Cannot call method getName() on mixed.
        $routename = ''; // Request::route()->getName();
        $old_act_route = last(explode('.', $routename));
        if (! \is_string($old_act_route)) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        $routename_act = Str::before($routename, $old_act_route).''.$act;
        $route_current = \Route::current();
        $route_params = [];
        if (null !== $route_current) {
            $route_params = $route_current->parameters();
            $routename = $route_current->getName();
        }
        /*
        try {
            $route_params = optional(\Route::current())->parameters();
        } catch (\Exception $e) {
            $route_params = [];
        }
        */
        if (\Route::has($routename_act)) {
            $parz = array_merge($route_params, [$row]);
            $parz = array_merge($parz, $query);
            $route = route($routename_act, $parz);
        } else {
            $route = '#'.$routename_act;
        }

        return $route;
    }

    /* // move to RoutePanelService
    public static function urlPanel(array $params){
        extract($params);

        if (! isset($panel)) {
            dddx(['err' => 'panel is missing']);

            return;
        }
        if (! isset($act)) {
            dddx(['err' => 'act is missing']);

            return;
        }

        $parents = $panel->getParents();

        $container_root = $panel->getRow();
        if ($parents->count() > 0) {
            $container_root = $parents->first()->row;
        }
        $n = 0;
        $parz = ['n' => $n + $parents->count(), 'act' => $act];
        if (isset($in_admin)) {
            $parz['in_admin'] = $in_admin;
        }
        $route_name = self::getRoutenameN($parz);

        $route_current = Route::current();
        $route_params = is_object($route_current) ? $route_current->parameters() : [];

        $i = 0;
        foreach ($parents as $parent) {
            $route_params['container'.($n + $i)] = $parent->postType();
            $route_params['item'.($n + $i)] = $parent->guid();
            ++$i;
        }

        $post_type = $panel->postType();
        //
        //if( $post_type==null) {
        //    $post_type=Str::snake(class_basename($panel->getRow()));
        //
        //    if($panel->getParent()!=null){
        //        $parent_post_type=Str::snake(class_basename($panel->getParent()->row));
        //        if(Str::startsWith($post_type,$parent_post_type.'_')){
        //            $post_type=Str::after($post_type,$parent_post_type.'_');
        //        }
        //    }
        //}


        $route_params['container'.($n + $i)] = $panel->postType();

        $route_params['item'.($n + $i)] = $panel->guid();

        if (inAdmin($params) && ! isset($route_params['module'])) {
            $container0 = $route_params['container0'];
            $model = xotModel($container0);
            $module_name = getModuleNameFromModel($model);
            $route_params['module'] = strtolower($module_name);
        }

        try {
            $route = route($route_name, $route_params, false);
        } catch (\Exception $e) {
            if (request()->input('debug', false)) {
                dddx(
                ['e' => $e->getMessage(),
                    'params' => $params,
                    'route_name' => $route_name,
                    'route_params' => $route_params,
                    'last row' => $panel->getRow(),
                    'panel post type' => $panel->postType(),
                    'panel guid' => $panel->guid(),
                    'last route key ' => $panel->getRow()->getRouteKey(),
                    'last route key name' => $panel->getRow()->getRouteKeyName(),
                    'in_admin' => config()->get('in_admin'),
                    'in_admin_session' => session()->get('in_admin'),
                    //'routes' => \Route::getRoutes(),
                ]
            );
            }

            return '#['.__LINE__.']['.__FILE__.']';
        }

        //--- aggiungo le query string all'url corrente
        $queries = collect(request()->query())->except(['_act', 'item0', 'item1'])->all();

        $url = url_queries($queries, $route);

        if (Str::endsWith($url, '?')) {
            $url = Str::before($url, '?');
        }

        return $url;
    }
    */
    // se n=0 => 'container0'
    // se n=1 => 'containers.container1'

    /**
     * @return string
     */
    public static function getRoutenameN(array $params)
    {
        // default vars
        $n = 0;
        $act = 'show';
        extract($params);
        $tmp = [];
        // dddx(inAdmin());
        if (inAdmin($params)) {
            $tmp[] = 'admin';
        }
        for ($i = 0; $i <= $n; ++$i) {
            $tmp[] = 'container'.$i;
        }
        $tmp[] = $act;
        $routename = implode('.', $tmp);

        return $routename;
    }

    /*
    public static function urlRelatedPanel(array $params){
        $act = 'show';
        extract($params);
        if (! isset($panel)) {
            dddx(['err' => 'panel is missing']);

            return;
        }
        if (! isset($related_name)) {
            dddx(['err' => 'related_name is missing']);

            return;
        }
        $parents = collect([]);
        $panel_curr = $panel;

        while (null != $panel_curr->getParent()) {
            $parents->prepend($panel_curr->getParent());
            $panel_curr = $panel_curr->getParent();
        }
        $container_root = $panel->getRow();
        if ($parents->count() > 0) {

            //$tmp='['.$parents->count().']';
            //foreach($parents as $parent){
            //    $tmp.=$parent->getRow()->post_type.'-';
            //}
            //return $tmp;

            $container_root = $parents->first()->row;
        }

        //$containers_class = self::getContainersClass();
        //$n = collect($containers_class)->search(get_class($container_root));
        //if (null === $n) {
        //    $n = 0;
        //}

        $n = 0;

        $route_name = self::getRoutenameN(['n' => $n + 1 + $parents->count(), 'act' => $act]);
        $route_current = \Route::current();
        $route_params = is_object($route_current) ? $route_current->parameters() : [];

        $i = 0;
        foreach ($parents as $parent) {
            $route_params['container'.($n + $i)] = $parent->postType();
            $route_params['item'.($n + $i)] = $parent->guid();
            ++$i;
        }
        $route_params['container'.($n + $i)] = $panel->postType();
        $route_params['item'.($n + $i)] = $panel->guid();
        ++$i;
        $route_params['container'.($n + $i)] = $related_name;

        $route_params['page'] = 1;
        $route_params['_act'] = '';
        unset($route_params['_act']);
        try {
            $url = str_replace(url(''), '', route($route_name, $route_params));
        } catch (\Exception $e) {
            if (request()->input('debug', false)) {
                dd([
                    'route_name' => $route_name,
                    'route_params' => $route_params,
                    'line' => __LINE__,
                    'file' => __FILE__,
                    'e' => $e->getMessage(),
                ]);
            }

            return '#['.__LINE__.']['.__FILE__.']';
        }

        return $url;
    }
    */

    /**
     * @return string
     */
    public static function urlLang(array $params = [])
    {
        extract($params);

        return '?';
        /*
        return '?'.$lang; //da fixare dopo
        //$row=$this->row;
        //$row->lang=$lang;
        //return '/wip'.$this->url();
        $route_name = \Route::currentRouteName();
        $route_params = optional(\Route::current())->parameters();
        $route_params['lang'] = $lang;
        [$containers, $items] = params2ContainerItem($route_params);
        $n_items = count($items);
        //dddx($n_items);//1
        //dddx($route_name); containers.show
        for ($i = 0; $i < $n_items; ++$i) {
            $v = $items[$i];
            if (method_exists($v, 'postLang')) {
                $tmp = $v->postLang($lang)->first();
                if (is_object($tmp)) {
                    $guid = $tmp->guid;
                } else {
                    $guid = '#';
                    //dddx(app()->getLocale());
                    $v_post = $v->post;
                    if (null == $v_post) {
                        break;
                    }
                    $new_post = $v_post->replicate();
                    $fields = ['title', 'subtitle', 'txt', 'meta_description', 'meta_keywords'];
                    foreach ($fields as $field) {
                        $trans = ImportService::trans(['q' => $new_post->$field, 'from' => app()->getLocale(), 'to' => $lang]);

                        //dddx([
                        //    'from'=>app()->getLocale(),
                        //    'to'=>$lang,
                        //    'trans'=>$trans,

                        //]);

                        $new_post->$field = $trans;
                    }
                    $new_post->lang = $lang;
                    $new_post->save();
                    $guid = $new_post->guid;
                }
            } else {
                $route_key_name = $v->getRouteKeyName();
                $guid = $v->$route_key_name;
            }

            $route_params['item'.$i] = $guid;
            //dddx($route_params['item'.$i]->guidLang);
        }
        //dddx($route_params);
        //return '/wip['.__LINE__.']['.__FILE__.']';
        try {
            return route($route_name, $route_params);
        } catch (\Exception $e) {
            return url($lang);
        }
        */
    }

    /**
     * Function getAct.
     *
     * @throws \Exception
     */
    public static function getAct(): string
    {
        $route_action = \Route::currentRouteAction();
        if (null === $route_action) {
            throw new \Exception('$route_action is null');
        }
        $act = Str::after($route_action, '@');

        // --- i prossimi 2 if son per i controller con metodo invoke
        if (Str::contains($act, '\\')) {
            $act = Str::afterLast($act, '\\');
        }
        if (Str::endsWith($act, 'Controller')) {
            $act = Str::before($act, 'Controller');
        }
        $act = Str::snake($act);

        return $act;
    }

    /**
     * Function.
     *
     * @throws \Exception
     */
    public static function getModuleName(): string
    {
        $route_action = \Route::currentRouteAction();
        if (null === $route_action) {
            throw new \Exception('$route_action is null');
        }
        $mod_name = Str::between($route_action, 'Modules\\', '\Http');

        return $mod_name;
    }

    /**
     * Function.
     *
     * @throws \Exception
     */
    public static function getControllerName(): string
    {
        $route_action = \Route::currentRouteAction();
        if (null === $route_action) {
            throw new \Exception('$route_action is null');
        }
        $name = Str::between($route_action, 'Http\Controllers\\', 'Controller');

        return $name;
    }

    public static function getView(): string
    {
        $tmp = self::getControllerName();
        $tmp_arr = explode('\\', $tmp);

        $params = getRouteParameters();
        [$containers, $items] = params2ContainerItem($params);

        $params['containers'] = implode('.', $containers);
        $path = collect($tmp_arr)
            ->filter(
                function ($item) {
                    return ! \in_array($item, ['Module', 'Item'], true);
                }
            )
            ->map(
                function ($item) use ($params) {
                    $item = Str::snake($item);

                    return $params[$item] ?? $item;
                }
            )->implode('.');

        return $path;
    }
}

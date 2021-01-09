<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Xot\Contracts\PanelContract;

/**
 * Class PanelRouteService
 * @package Modules\Xot\Services
 */
class PanelRouteService {
    /**
     * @var
     */
    public $panel;
    /*
    public function __construct(&$panel) {
        $this->panel = $panel;
    }
    */

    /**
     * setPanel.
     *
     * @param PanelContract $panel
     *
     * @return $this
     */
    public function setPanel(PanelContract &$panel) {
        $this->panel = $panel;

        return $this;
    }

    /**
     * @param array $params
     * @return array|bool|mixed
     */
    public static function inAdmin($params = []) {
        if (isset($params['in_admin'])) {
            return $params['in_admin'];
        }
        //dddx(ThemeService::__getStatic('in_admin'));
        if (null !== config()->get('in_admin')) {
            return config()->get('in_admin');
        }
        if ('admin' == \Request::segment(1)) {
            return true;
        }
        $segments = (\Request::segments());
        if (count($segments) > 0 && 'livewire' == $segments[0]) {
            if (true == session()->get('in_admin')) {
                return true;
            }
        }

        return false;
        //dddx(session('in_admin'));
        /*
        $segments = request()->segments();
        dddx($_SERVER);

        return 'admin' == 'aa';
        */
        //return in_admin();
    }

    /**
     * @param $params
     * @return string|void
     */
    public function urlPanel($params) {
        $panel = $this->panel;
        extract($params);
        /*
        if (! isset($panel)) {
            dddx(['err' => 'panel is missing']);

            return;
        }
        */
        if (! isset($act)) {
            dddx(['err' => 'act is missing']);

            return;
        }
        $parents = $panel->getParents();

        $container_root = $panel->row;
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
        /*
        if( $post_type==null) {
            $post_type=Str::snake(class_basename($panel->row));

            if($panel->getParent()!=null){
                $parent_post_type=Str::snake(class_basename($panel->getParent()->row));
                if(Str::startsWith($post_type,$parent_post_type.'_')){
                    $post_type=Str::after($post_type,$parent_post_type.'_');
                }
            }
        }
        */

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
                    'last row' => $panel->row,
                    'panel post type' => $panel->postType(),
                    'panel guid' => $panel->guid(),
                    'last route key ' => $panel->row->getRouteKey(),
                    'last route key name' => $panel->row->getRouteKeyName(),
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

    //se n=0 => 'container0'
    // se n=1 => 'container0.container1'

    /**
     * @param $params
     * @return string
     */
    public static function getRoutenameN($params) {
        //default vars
        $n = 0;
        $act = 'show';
        extract($params);
        $tmp = [];
        //dddx(inAdmin());
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

    /**
     * @param $params
     * @return string|string[]|void
     */
    public function urlRelatedPanel($params) {
        $panel = $this->panel;
        $act = 'show';
        extract($params);

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
        $container_root = $panel->row;
        if ($parents->count() > 0) {
            /*
            $tmp='['.$parents->count().']';
            foreach($parents as $parent){
                $tmp.=$parent->row->post_type.'-';
            }
            return $tmp;
            */
            $container_root = $parents->first()->row;
        }
        /*
        $containers_class = self::getContainersClass();
        $n = collect($containers_class)->search(get_class($container_root));
        if (null === $n) {
            $n = 0;
        }
        */
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

    /**
     * @param array $params
     * @return string
     */
    public static function urlLang($params = []) {
        extract($params);

        return '?';
        /*
        return '?'.$lang; //da fixare dopo
        //$row=$this->row;
        //$row->lang=$lang;
        //return '/wip'.$this->url();
        $route_name = \Route::currentRouteName();
        $route_params = \Route::current()->parameters();
        $route_params['lang'] = $lang;
        [$containers, $items] = params2ContainerItem($route_params);
        $n_items = count($items);
        //ddd($n_items);//1
        //ddd($route_name); container0.show
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
            //ddd($route_params['item'.$i]->guidLang);
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
}

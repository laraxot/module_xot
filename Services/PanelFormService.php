<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Collective\Html\FormFacade as Form;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\FormX\Services\FormXService;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Contracts\PanelContract;

/**
 * Class PanelFormService.
 */
class PanelFormService {
    protected PanelContract $panel;

    /*
    public function __construct(&$panel) {
        $this->panel = $panel;
    }
    */

    /**
     * setPanel.
     *
     * @return $this
     */
    public function setPanel(PanelContract &$panel) {
        $this->panel = $panel;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function formCreate($params = []) {
        $fields = $this->createFields();
        $row = $this->panel->row;
        $res = '';
        //$res.='<h3>'.$this->storeUrl().'</h3>'; //4 debug
        $res .= Form::bsOpenPanel($this, 'store');
        $res .= '<div class="clearfix">';
        foreach ($fields as $field) {
            $res .= ThemeService::inputHtml(['row' => $row, 'field' => $field]);
        }
        $res .= '</div>';
        //$res.=Form::bsSubmit('save');
        $res .= '<p class="form-submit">
            <input name="submit" type="submit" id="submit" value="Post your answer" class="button small color">
        </p>';
        $res .= Form::close();

        return $res;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function formEdit($params = []) {
        $submit_btn = '<p class="form-submit">
            <input name="submit" type="submit" id="submit" value="Post your answer" class="button small color">
        </p>';
        extract($params);
        $fields = $this->editFields();
        $row = $this->panel->row;
        $res = '';
        //$res.='<h3>'.$this->storeUrl().'</h3>'; //4 debug
        $res .= Form::bsOpenPanel($this, 'update');
        $res .= '<div class="clearfix">';
        foreach ($fields as $field) {
            $res .= ThemeService::inputHtml(['row' => $row, 'field' => $field]);
        }
        $res .= '</div>';
        //$res.=Form::bsSubmit('save');
        $res .= $submit_btn;
        $res .= Form::close();

        return $res;
    }

    /*
    public function btnDelete($params = []) {
        $class = 'btn-primary mb-2';
        extract($params);
        //dddx($params);
        $act = 'destroy';
        $parz = [
            'id' => $this->panel->row->getKey(),
            'btn_class' => 'btn '.$class,
            'route' => $this->panel->url(['act' => 'destroy']),
            'act' => $act,
            'title' => $title,
        ];

        return view('formx::includes.components.btn.'.$act)->with($parz);
    }

    public function btnDetach($params = []) {
        $class = 'btn-primary mb-2';
        extract($params);
        $act = 'detach';
        $parz = [
            'id' => $this->panel->row->getKey(),
            'btn_class' => 'btn '.$class,
            'route' => $this->detachUrl(),
            'act' => $act,
        ];

        return view('formx::includes.components.btn.'.$act)->with($parz);
    }
    */

    /**
     * @param array $params
     *
     * @return string
     */
    public function btnCrud($params = []) {
        extract($params);
        $acts = ['edit', 'destroy', 'show'];
        if (is_object($this->panel->row->pivot)) {
            $acts = ['edit', 'destroy', 'detach', 'show'];
        }

        $html = '';
        if (! in_array('title', array_keys($params))) {
            $params['title'] = '';
        }
        foreach ($acts as $act) {
            $params['act'] = $act;
            $html .= $this->btnHtml($params);
        }
        if (in_array('group', array_keys($params)) && false == $params['group']) {
        } else {
            $html = '<div role="group" aria-label="Actions" class="btn-group btn-group-sm">'.
            chr(13).$html.chr(13).'</div>';
        }

        return $html;
    }

    /**
     * @param array $params
     *
     * @return string|void|null
     */
    public function btnHtml($params) {
        $params['panel'] = $this->panel;
        //$params['url'] = RouteService::urlPanel($params);
        $params['url'] = $this->panel->route->urlPanel($params);
        $params['method'] = Str::camel($params['act']);
        if ('index_order' == $params['act']) {
            //  dddx($params);
        }

        if (! isset($params['tooltip'])) {
            $row = $this->panel->row;
            $module_name_low = strtolower(getModuleNameFromModel($row));
            $params['tooltip'] = trans($module_name_low.'::'.strtolower(class_basename($row)).'.act.'.$params['method']);
        }

        if (! isset($params['title'])) {
            $row = $this->panel->row;
            $module_name_low = strtolower(getModuleNameFromModel($row));

            $trans_key = $module_name_low.'::'.strtolower(class_basename($row)).'.act.'.$params['method'];
            $trans = trans($trans_key);
            $title = $trans;
            if ($trans == $trans_key && ! config('xra.show_trans_key')) {
                $title = class_basename($row); //.' '.$params['method'];
            }

            $params['title'] = $title;
        }

        if (! isset($params['icon'])) {
            switch ($params['method']) {
                case 'create':
                    $params['icon'] = '<i class="far fa-plus-square"></i>';
                    break;
                case 'edit':
                    $params['icon'] = '<i class="far fa-edit"></i>';
                    break;
                case 'destroy':
                    $params['icon'] = '<i class="far fa-trash-alt"></i>';
                    break;
                case 'show':
                    $params['icon'] = '<i class="far fa-eye"></i>';
                    break;
                 case 'indexOrder':
                    $params['icon'] = '<i class="fas fa-sort"></i>';
                    break;
                default:
                    //$params['icon'] = $params['method']; //per vedere quale
                    break;
            }
        }

        if (true === $params['title']) {
            $row = $this->panel->row;
            $module_name_low = strtolower(getModuleNameFromModel($row));
            $parent = $this->panel->getParent();
            if (null != $parent) {
                $tmp = [];
                $tmp[] = class_basename($parent->row);
                $tmp[] = class_basename($row);
                $tmp[] = 'act';
                $tmp[] = $params['method'];
                $tmp = collect($tmp)->map(function ($item) {
                    return Str::snake($item);
                })->implode('.');
                $params['title'] = trans($module_name_low.'::'.$tmp);
            } else {
                $params['title'] = trans($module_name_low.'::'.strtolower(class_basename($row)).'.act.'.$params['method']);
            }
        }

        return FormXService::btnHtml($params);
    }

    /*
    public function btn($act, $params = []) {
        dddx('deprecated');
        extract($params);
        $parents = [];
        $parent = $this->panel->parent;
        $route_params = \Route::current()->parameters();
        $cont_i = RouteService::containerN(['model' => get_class($parent->row)]);
        $routename = RouteService::routenameN(['n' => $cont_i + 1, 'act' => $act]);

        $route_params['item'.($cont_i + 0)] = $this->parent->row;
        $route_params['container'.($cont_i + 1)] = $this->postType();
        $route_params['item'.($cont_i + 1)] = $this->panel->row;
        $route = route($routename, $route_params);
        //http://multi.local:8080/it/profile/profile%20279656/restaurant/pizza%20gino/cuisine/antipasti/recipe/gigi]
        //return '['.$routename.']<br>['.$route.'][['.$cont_i.']';
        $parz = [
            'id' => $this->panel->row->id,
            'btn_class' => 'btn',
            'route' => $route,
            'act' => $act,
        ];
        if (isset($modal) && $modal) {
            return view('formx::includes.components.btn.modal')->with($parz);
        }

        return view('formx::includes.components.btn.'.$act)->with($parz);
    }
    */
    /* deprecated
    public function btnSubmit($params = []) {
        return Form::bsSubmit(trans('xot::buttons.save'));
    }
    */

    /**
     * @param array $params
     *
     * @return array
     */
    public function exceptFields($params = []) {
        $act = 'show';
        $panel = $this->panel;
        extract($params);
        $excepts = collect([]);
        if (is_object($panel->rows)) {
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
                if (method_exists($panel->rows, $method)) {
                    $excepts = $excepts->merge($panel->rows->$method());
                }
            }
        }
        $excepts = $excepts->unique()->all();

        $fields = collect($panel->fields())
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

    /**
     * @return array
     */
    public function indexFields() {
        $fields = $this->exceptFields(['act' => 'index']);

        return $fields;
    }

    /**
     * @return array
     */
    public function createFields() {
        $fields = $this->exceptFields(['act' => 'create']);

        return $fields;
    }

    /**
     * @return array
     */
    public function editFields() {
        $fields = $this->exceptFields(['act' => 'edit']);

        return $fields;
    }

    /**
     * @return array
     */
    public function indexEditFields() {
        $fields = $this->exceptFields(['act' => 'index_edit']);

        return $fields;
    }
}

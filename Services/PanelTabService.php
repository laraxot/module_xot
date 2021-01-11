<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Modules\Xot\Models\Panels\XotBasePanel;

/**
 * Class PanelTabService.
 */
class PanelTabService {
    protected XotBasePanel $panel;

    /**
     * PanelTabService constructor.
     */
    public function __construct(XotBasePanel &$panel) {
        $this->panel = $panel;
    }

    public function getItemTabs(): array {
        $item = $this->panel->row;
        $tabs = $this->panel->tabs();
        $routename = \Route::currentRouteName();
        $act = last(explode('.', $routename));
        $row = [];
        foreach ($tabs as $tab) {
            $tmp = new \stdClass();
            $tmp->title = $tab;

            if (in_array($act, ['index_edit', 'edit', 'update'])) {
                $tab_act = 'index_edit';
            } else {
                $tab_act = 'index';
            }
            $tmp->url = $this->panel->relatedUrl(['related_name' => $tab, 'act' => $tab_act]);
            $tmp->active = false; //in_array($tab,$containers);
            $row[] = $tmp;
        }

        return [$row];
    }

    public function getRowTabs(): array {
        $data = [];
        foreach ($this->panel->tabs() as $tab) {
            $tmp = (object) [];
            $tmp->title = $tab;
            $tmp->url = $this->panel->relatedUrl(['related_name' => $tab, 'act' => 'index']);
            $tmp->index_edit_url = $this->panel->relatedUrl(['related_name' => $tab, 'act' => 'index_edit']);
            $tmp->create_url = $this->panel->relatedUrl(['related_name' => $tab, 'act' => 'create']);
            $tmp->active = false;
            $data[] = $tmp;
        }

        return $data;
    }

    public function getTabs(): array {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $act = last(explode('.', $routename));
        //$routename = \Route::current()->getName();
        $route_params = \Route::current()->parameters();
        [$containers, $items] = params2ContainerItem($route_params);
        $data = [];
        //$items[]=$this->row;
        if (! is_array($items)) {
            return [];
        }
        //array_unique($items);
        $parents = $this->panel->getParents();
        if ('' != $this->panel->guid()) {
            $parents->push($this->panel);
        }
        //dddx($parents);

        foreach ($parents as $k => $panel) {
            //$item = $panel->row;
            $tabs = [];
            if (! is_object($panel)) {
                return $tabs;
            }
            $tabs = $panel->tabs();
            $row = [];
            //*
            if (0 == $k) {
                if (Gate::allows('index', $panel)) {
                    $tmp = new \stdClass();
                    $tmp->title = '<< Back '; //.'['.get_class($item).']';
                    $tmp->url = $panel->indexUrl();
                    $tmp->active = false;
                    $row[] = $tmp;
                }
                //-----------------------
                $tmp = new \stdClass();
                if (in_array($act, ['index_edit', 'edit', 'update'])) {
                    $url = $panel->editUrl();
                } else {
                    $url = $panel->showUrl();
                }
                $tmp->url = $url;
                $tmp->title = 'Content '; //.'['.request()->url().']['.$url.']';
                /*
                if ($url_test = 1) {
                    $tmp->active = request()->url() == $url;
                } else {
                    $tmp->active = request()->routeIs('admin.container0.'.$act);
                }
                */
                $tmp->active = request()->url() == $url;
                if (null != $panel->guid()) {
                    $row[] = $tmp;
                }
                //----------------------
            }
            //*/

            foreach ($tabs as $tab) {
                //dddx($tabs);
                $tmp = new \stdClass();

                if (! is_array($tab)) {
                    //$tmp = new \stdClass();
                    $tmp->title = $tab;
                    $tmp->panel = $panel;

                    if (in_array($act, ['index_edit', 'edit', 'update'])) {
                        $tab_act = 'index_edit';
                    } else {
                        $tab_act = 'index';
                    }
                    $tmp->url = $panel->relatedUrl(['related_name' => $tab, 'act' => $tab_act]);
                    $tmp->active = in_array($tab, $containers);
                } else {
                    //  dddx($tmp);
                    //$tmp = new \stdClass();
                    $tmp->title = $tab['title'];
                    $panel1 = $panel;
                    if (isset($tab['related'])) {
                        $panel1 = $panel1->related($tab['related']);
                    }
                    if (isset($tab['container_action'])) {
                        $tmp->url = $panel1->containerAction($tab['container_action'])->url();
                    }
                    //$tmp->url = $tab['page'];
                    $tmp->active = false;
                }
                $row[] = $tmp;
            }

            $data[] = $row;
        }
        //dddx([$data, $tabs]);

        return $data;
    }
}

<?php

namespace Modules\Xot\Presenters;

use Illuminate\Support\Collection;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Contracts\PanelPresenterContract;

/**
 * Class HtmlPanelPresenter
 * @package Modules\Xot\Presenters
 */
class HtmlPanelPresenter implements PanelPresenterContract {
    /**
     * @var
     */
    protected $panel;

    /**
     * @param \Modules\Xot\Contracts\PanelContract $panel
     * @return mixed|void
     */
    public function setPanel(\Modules\Xot\Contracts\PanelContract &$panel) {
        $this->panel = $panel;
    }

    /**
     * @param Collection|null $items
     * @return mixed|void
     */
    public function index(?Collection $items) {
        /*
        $count = $items->count();
        $last_update = $items
            ->sortByDesc
            ->created_at
            ->first()
            ->created_at
            ->format('d/m/Y');

        $data = [
            'Numero elementi' => $count,
            'Ultimo aggiornamento' => $last_update,
        ];

        return view('workshop::index')->with(compact('items', 'data'));
        */
    }

    /**
     * @param null $params
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function out($params = null) {
        //$route_params = \Route::current()->parameters();

        [$containers, $items] = params2ContainerItem();
        $view = ThemeService::getView(); //vew che dovrebbe essere
        $view_work = ThemeService::getViewWork(); //view effettiva
        $views = ThemeService::getDefaultViewArray(); //views possibili

        $mod_trad = $this->panel->getModuleNameLow().'::'.last($containers);

        //--- per passare la view all'interno dei componenti
        \View::composer(
            '*',
            function ($view_params) use ($view) {
                \View::share('view', $view);
                $trad = implode('.', array_slice(explode('.', $view), 0, -1));
                \View::share('trad', $trad);
                \View::share('lang', \App::getLocale());
                //\View::share('mod_trad', $mod_trad);
            }
        );

        $modal = null;
        if (\Request::ajax()) {
            $modal = 'ajax';
        } elseif ('iframe' == \Request::input('format')) {
            $modal = 'iframe';
        }

        //$rows = $this->panel->rows()->paginate(20);

        //*
        $rows_err = '';
        try {
            //dddx($this->panel->rows());
            $rows = $this->panel->rows()->paginate(20);
        } catch (\Exception $e) {
            $rows = null;
            $rows_err = $e;
        } catch (\Error $e) {
            $rows = null;
            $rows_err = $e;
        }
        //*/

        $view_params = [
            'view' => $view,
            'view_work' => $view_work,
            'views' => $views,
            '_panel' => $this->panel,
            'row' => $this->panel->row,
            'rows' => $rows,
            'rows_err' => $rows_err,
            'mod_trad' => $mod_trad,
            'trad_mod' => $mod_trad, /// da sostiutire ed uccidere
            'params' => \Route::current()->parameters(),
            'routename' => \Route::current()->getName(),
            'modal' => $modal,
            'containers' => $containers,
            'items' => $items,
            'page' => new \Modules\Theme\Services\Objects\PageObject(),
        ];

        return view($view_work)->with($view_params);
    }
}

<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Request;
use Route;

use function Safe\date;

/**
 * Class NavService.
 */
class NavService
{
    public static function yearNav(): Renderable
    {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        // $request->route('parameter_name')
        // $request->route()->paremeters()
        // 20     Cannot call method parameters() on mixed
        // $paz = request()->route()->parameters();
        $route_current = \Route::current();
        $params = [];
        if (null !== $route_current) {
            $params = $route_current->parameters();
        }
        $year = $request->input('year', date('Y'));
        $year = $year - 1;
        $nav = [];
        for ($i = 0; $i < 3; ++$i) {
            $tmp = [];
            $params['year'] = $year;
            $tmp['title'] = $year;
            // Strict comparison using === between numeric-string and (float|int) will always evaluate to false
            // if (date('Y') === $params['year']) {
            if (intval($params['year']) === intval(date('Y'))) {
                $tmp['title'] = '['.$tmp['title'].']';
            }
            if ($year === $params['year']) {
                $tmp['active'] = 1;
            } else {
                $tmp['active'] = 0;
            }

            if (null === $routename) {
                throw new \Exception('routename is null');
            }
            $tmp['url'] = route($routename, $params);
            $nav[] = (object) $tmp;
            ++$year;
        }

        /**
         * @phpstan-var view-string
         */
        $view = 'adm_theme::layouts.partials.nav';
        $view_params = [
            'nav' => $nav,
        ];

        return view($view, $view_params);
    }

    public static function monthYearNav(): Renderable
    {
        // possiamo trasformarlo in una macro
        $request = \Request::capture();
        $routename = \Route::currentRouteName();

        $route_current = \Route::current();
        $params = [];
        if (null !== $route_current) {
            $params = $route_current->parameters();
        }

        $year = $request->input('year', date('Y')) * 1;
        $month = $request->input('month', date('m')) * 1;

        $q = 2;
        $date = Carbon::create($year, $month, 1);
        if (false === $date) {
            throw new \Exception('carbon error');
        }
        $d = $date->subMonths($q);
        $nav = [];
        for ($i = 0; $i < ($q * 2) + 1; ++$i) {
            $tmp = [];
            $params['month'] = (int) $d->format('m');
            $params['year'] = (int) $d->format('Y');
            $tmp['title'] = $d->isoFormat('MMMM YYYY');
            if (date('Y') == $params['year'] && date('m') == $params['month']) {
                $tmp['title'] = '['.$tmp['title'].']';
            }
            if ($year === $params['year'] && $month === $params['month']) {
                $tmp['active'] = 1;
            } else {
                $tmp['active'] = 0;
            }
            if (null === $routename) {
                throw new \Exception('routename is null');
            }
            $tmp['url'] = route($routename, $params);
            $nav[] = (object) $tmp;
            $d->addMonth();
        }

        /**
         * @phpstan-var view-string
         */
        $view = 'adm_theme::layouts.partials.nav';
        $view_params = [
            'nav' => $nav,
        ];

        return view($view, $view_params);
    }

    /* deprecated
    public static function yearNavRedirect() {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $params = optional(\Route::current())->parameters();
        $year = $request->input('year', date('Y'));
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
    */
}

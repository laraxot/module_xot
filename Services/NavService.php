<?php

namespace Modules\Xot\Services;

class NavService {
    public static function yearNav() {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $params = \Route::current()->parameters();
        $year = $request->input('year', date('Y'));
        $year = $year - 1;
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

    public static function monthYearNav() { //possiamo trasformarlo in una macro
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $params = \Route::current()->parameters();
        /*
        if ('' == $request->year) {
            $request->year = date('Y');
        }
        if ('' == $request->month) {
            $request->month = date('m');
        }
        */
        $q = 2;
        $d = Carbon::create($request->year, $request->month, 1)->subMonth($q);
        $nav = [];
        for ($i = 0; $i < ($q * 2) + 1; ++$i) {
            $tmp = [];
            $params['month'] = $d->format('m') * 1;
            $params['year'] = $d->format('Y') * 1;
            $tmp['title'] = $d->isoFormat('MMMM YYYY');
            if (date('Y') == $params['year'] && date('m') == $params['month']) {
                $tmp['title'] = '['.$tmp['title'].']';
            }
            if ($request->year == $params['year'] && $request->month == $params['month']) {
                $tmp['active'] = 1;
            } else {
                $tmp['active'] = 0;
            }
            $tmp['url'] = route($routename, $params);
            $nav[] = (object) $tmp;
            $d->addMonth();
        }

        return $nav;
        //$d->locale() //it !!
        /*
        return '';
        */
    }

    public static function yearNavRedirect() {
        $request = \Request::capture();
        $routename = \Route::currentRouteName();
        $params = \Route::current()->parameters();

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
}

<?php

declare(strict_types=1);

namespace Modules\Xot\View\Composers;

// use App\Repositories\UserRepository;
use Illuminate\View\View;
use Modules\Xot\Services\FileService;
use Nwidart\Modules\Facades\Module;

/**
 * Class XotComposer.
 */
class XotComposer
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $lang = app()->getLocale();
        $view->with('lang', $lang);
        $view->with('_theme', $this);
    }

    public function asset(string $str): string
    {
        $url = Module::asset($str);

        /*
        dddx([
            'str'=>$str, //ewall::js/schedule.js
            '1'=>Module::asset($str),  //ewall-cms-dev.egeatech.it/modules/ewall
            '2'=>Module::assetPath($str),// /var/www/vhosts/egeatech.it/ewall-cms-dev.egeatech.it/public_html/modules/ewall::js/schedule.js
            '3'=>Module::getModulePath($str),///var/www/vhosts/egeatech.it/ewall-cms-dev.egeatech.it/laravel/Modules/Ewall::js/schedule.js/
            //'4'=>Module::getPath($str),///var/www/vhosts/egeatech.it/ewall-cms-dev.egeatech.it/laravel/Modules
            //'5'=>Module::getAssetsPath($str),///var/www/vhosts/egeatech.it/ewall-cms-dev.egeatech.it/public_html/modules
        ]);
        */
        return asset(FileService::asset($str));
        /*
        $from=Module::getModulePath($str);
        $from=str_replace('::','/Resources/',$from);
        $to=Module::assetPath($str);
        $to=str_replace('::','/',$to);

        dddx([$from,$to]);
        */
    }
}

<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Theme\Services\ThemeService;

if (! defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

//----- TODO
//--  1) capire come far fare da chiamato non da consolle "scout:import"

/**
 * Class ArtisanService.
 */
class ArtisanService {
    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    public static function act(string $act) { //da fare anche in noconsole, e magari mettere un policy
        $module_name = \Request::input('module');
        switch ($act) {
            case 'migrate':
                \DB::purge('mysql');
                \DB::reconnect('mysql');
                if ('' != $module_name) {
                    echo '<h3>Module '.$module_name.'</h3>';

                    return ArtisanService::exe('module:migrate '.$module_name.' --force');
                } else {
                    return ArtisanService::exe('migrate --force');
                }
                // no break
            case 'routelist': return ArtisanService::exe('route:list');
            case 'routelist1': return ArtisanService::showRouteList();
            case 'optimize': return ArtisanService::exe('optimize');
            case 'clear':
                //echo self::debugbarClear();
                echo self::errorClear();
                echo ArtisanService::exe('cache:clear');
                echo ArtisanService::exe('config:clear');
                echo ArtisanService::exe('event:clear');
                echo ArtisanService::exe('route:clear');
                echo ArtisanService::exe('view:clear');
                echo ArtisanService::exe('debugbar:clear');
                echo ArtisanService::exe('opcache:clear');
                echo ArtisanService::exe('optimize:clear');
                echo ArtisanService::exe('key:generate');
                echo 'DONE';
            break;
            case 'clearcache': return ArtisanService::exe('cache:clear');
            case 'routecache': return ArtisanService::exe('route:cache');
            case 'routeclear': return ArtisanService::exe('route:clear');
            case 'viewclear': return ArtisanService::exe('view:clear');
            case 'configcache': return ArtisanService::exe('config:cache');
            //-------------------------------------------------------------------
            case 'debugbar:clear':
                self::debugbarClear();
            break;

            //------------------------------------------------------------------

            case 'module-list': return ArtisanService::exe('module:list');
            case 'module-disable': return ArtisanService::exe('module:disable '.$module_name);
            case 'module-enable': return ArtisanService::exe('module:enable '.$module_name);
            //----------------------------------------------------------------------
            case 'error':
            case 'error-show':
                return ArtisanService::errorShow();
            case 'error-clear':
                 return self::errorClear();

            //-------------------------------------------------------------------------
            case 'spatiecache-clear':
                /* da vedere se e' necessaria
                try {
                    return \Spatie\ResponseCache\Facades\ResponseCache::clear();
                } catch (\Exception $e) {
                    dddx($e);
                }
                */
            //case 'spatiecache-clear1': return ArtisanService::exe('responsecache:clear'); //The command "responsecache:clear" does not exist.

            default: return '';
        }

        return '';
    }

    public static function errorShow() {
        $view = 'xot::acts.artisan.error-show';
        $files = File::files(storage_path('logs'));
        $log = request('log', '');
        $content = '';
        if ('' != $log) {
            if (File::exists(storage_path('logs/'.$log))) {
                $content = File::get(storage_path('logs/'.$log));
            }
        }
        $pattern = '/url":"([^"]*)"/';
        preg_match_all($pattern, $content, $matches);

        $urls = [];
        if (isset($matches[1])) {
            $urls = array_unique($matches[1]);
        }

        $view_params = [
            'view' => $view,
            'lang' => app()->getLocale(),
            'files' => $files,
            'content' => $content,
            'urls' => $urls,
        ];

        return view()->make($view, $view_params);
    }

    public static function showRouteList() {
        $routeCollection = Route::getRoutes();
        /*
        $view = ThemeService::getViewModule();

        dddx([
            'view' => $view,
            'this' => get_class(),
            'parent' => get_parent_class(),
            'debug' => \debug_backtrace(),
        ]);
        */
        /*
        $debug = \debug_backtrace();
        $file = $debug[1]['file'];

        dddx([
            'file' => $file,
            'views' => ThemeService::getDefaultViewArray(),
        ]);
        */
        $view = 'xot::acts.artisan.show_route_list';
        $view_params = [
            'view' => $view,
            'routeCollection' => $routeCollection,
            'lang' => app()->getLocale(),
        ];

        return view()->make($view, $view_params);
    }

    /**
     * @return string
     */
    public static function errorClear() {
        $files = File::files(storage_path('logs'));

        foreach ($files as $file) {
            if ('log' == $file->getExtension() && false !== $file->getRealPath()) {
                //Parameter #1 $paths of static method Illuminate\Filesystem\Filesystem::delete() expects array|string, Symfony\Component\Finder\SplFileInfo given.
                echo '<br/>'.$file->getRealPath();

                File::delete($file->getRealPath());
            }
        }

        return '<pre>laravel.log cleared !</pre> ('.count($files).' Files )';
    }

    /**
     * @return string
     */
    public static function debugbarClear() {
        $files = File::files(storage_path('debugbar'));
        foreach ($files as $file) {
            if ('json' == $file->getExtension() && false !== $file->getRealPath()) {
                echo '<br/>'.$file->getRealPath();

                File::delete($file->getRealPath());

                //$file->delete();
            }
        }

        return 'Debugbar Storage cleared! ('.count($files).' Files )';
    }

    /**
     * @param string $command
     */
    public static function exe($command, array $arguments = []): string {
        try {
            $output = '';

            Artisan::call($command, $arguments);
            $output .= '[<pre>'.Artisan::output().'</pre>]';

            return $output;  // dato che mi carico solo le route minime menufull.delete non esiste.. impostare delle route comuni.
        } catch (Exception $e) {
            //dddx(get_class_methods($e));
            $vendor_dir = (realpath(LARAVEL_DIR.'/vendor'));
            if (false === $vendor_dir) {
                throw new \Exception('not recognize realpath laravel_dir/vendor');
            }
            $my = collect($e->getTrace())->filter(
                function ($item) use ($vendor_dir) {
                    return isset($item['file']) && ! Str::startsWith($item['file'], $vendor_dir);
                }
            );

            //dddx([LARAVEL_DIR, $e->getTrace(), $e->getPrevious()]);
            //dddx($my);
            $msg = '<br/>'.$command.' non effettuato '.$e->getMessage().
                '<br/>Code: '.$e->getCode().
                '<br/>File: '.$e->getFile().
                '<br/>Line: '.$e->getLine();
            foreach ($my as $v) {
                $msg .= '<br/>My File :'.$v['file'].
                '<br/>My Line :'.$v['line'];
            }

            return $msg;
        } /*
        //Dead catch - Symfony\Component\Console\Exception\CommandNotFoundException is already caught by Exception above.
        catch (\Symfony\Component\Console\Exception\CommandNotFoundException $e) {
            return '<br/>'.$command.' non effettuato';
        }*/
    }
}

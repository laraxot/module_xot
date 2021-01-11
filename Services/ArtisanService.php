<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

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
            case 'optimize': return ArtisanService::exe('optimize');
            case 'clear':
                echo self::debugbarClear();
                echo self::errorClear();
                echo ArtisanService::exe('cache:clear');
                echo ArtisanService::exe('config:clear');
                echo ArtisanService::exe('event:clear');
                echo ArtisanService::exe('route:clear');
                echo ArtisanService::exe('view:clear');
                echo ArtisanService::exe('debugbar:clear');
                echo ArtisanService::exe('opcache:clear');
                echo ArtisanService::exe('optimize:clear');

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
            case 'error-show':
                $contents = '';
                $files = File::files(storage_path('logs'));
                echo '<h3>'.count($files).' Error Logs </h3>';
                echo '<ol>';
                foreach ($files as $file) {
                    //dddx(get_class_methods($file));
                    echo '<li><a href="?_act=artisan&cmd=error-show&log='.$file->getFilename().'">'.$file->getFilename().'</a></li>';
                }
                echo '</ol>';
                $log = \Request::input('log');
                if ('' != $log) {
                    if (File::exists(storage_path('logs/'.$log))) {
                        $contents = File::get(storage_path('logs/'.$log));
                    }
                }

                return '<pre>'.$contents.'</pre>';
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

    /**
     * @return string
     */
    public static function errorClear() {
        $files = File::files(storage_path('logs'));

        foreach ($files as $file) {
            if ('log' == $file->getExtension()) {
                File::delete($file);
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
            if ('json' == $file->getExtension()) {
                File::delete($file);
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
            return '<br/>'.$command.' non effettuato '.$e->getMessage();
        } /*
        //Dead catch - Symfony\Component\Console\Exception\CommandNotFoundException is already caught by Exception above.
        catch (\Symfony\Component\Console\Exception\CommandNotFoundException $e) {
            return '<br/>'.$command.' non effettuato';
        }*/
    }
}

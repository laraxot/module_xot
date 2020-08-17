<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

//----- TODO
//--  1) capire come far fare da chiamato non da consolle "scout:import"

class ArtisanService {
    public static function act($act) { //da fare anche in noconsole, e magari mettere un policy
        $module_name = \Request::input('module');
        switch ($act) {
            case 'migrate':
                \DB::purge('mysql');
                \DB::reconnect('mysql');

                return ArtisanService::exe('migrate');
            case 'routelist': return ArtisanService::exe('route:list');
            case 'optimize': return ArtisanService::exe('optimize');

            case 'clearcache': return ArtisanService::exe('cache:clear');
            case 'routecache': return ArtisanService::exe('route:cache');
            case 'routeclear': return ArtisanService::exe('route:clear');
            case 'viewclear': return ArtisanService::exe('view:clear');
            case 'configcache': return ArtisanService::exe('config:cache');

            //-------------------------------------------------------------------
            case 'debugbar:clear':
                $files=File::files(storage_path('debugbar'));
                foreach($files as $file){
                    if($file->getExtension()=='json'){
                        File::delete($file);
                    }
                }
               return 'Debugbar Storage cleared! ('.count($files).' Files )';
            break;

            //------------------------------------------------------------------

            case 'module-list': return ArtisanService::exe('module:list');
            case 'module-disable': return ArtisanService::exe('module:disable '.$module_name);
            case 'module-enable': return ArtisanService::exe('module:enable '.$module_name);
            //----------------------------------------------------------------------
            case 'error-show':
                $contents = File::get(storage_path('logs/laravel.log'));

                return '<pre>'.$contents.'</pre>';
            case 'error-clear':
                $contents = File::put(storage_path('logs/laravel.log'), '');

                return '<pre>laravel.log cleared !</pre>';

            //-------------------------------------------------------------------------
            case 'spatiecache-clear': return \Spatie\ResponseCache\Facades\ResponseCache::clear();
            //case 'spatiecache-clear1': return ArtisanService::exe('responsecache:clear'); //The command "responsecache:clear" does not exist.

            default: return '';
        }

        return '';
    }

    public static function exe($command, array $arguments = []) {
        try {
            $output = '';

            Artisan::call($command, $arguments);
            $output .= '[<pre>'.Artisan::output().'</pre>]';

            return $output;  // dato che mi carico solo le route minime menufull.delete non esiste.. impostare delle route comuni.
        } catch (Exception $e) {
            return '<br/>'.$command.' non effettuato';
        }
    }
}

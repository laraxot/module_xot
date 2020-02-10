<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Str;

class TenantService {
    public static function getName($params = []) {
        if (! isset($_SERVER['SERVER_NAME']) || '127.0.0.1' == $_SERVER['SERVER_NAME']) {
            $_SERVER['SERVER_NAME'] = 'localhost';
        }
        $server_name = Str::slug(\str_replace('www.', '', $_SERVER['SERVER_NAME']));
        if (! file_exists(base_path('config/'.$server_name))) {
            $server_name = 'localhost';
        }

        return $server_name;
    }

    //end function

    public static function filePath($filename) {
        $path = base_path('config/'.self::getName().'/'.$filename);
        $path = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);

        return $path;
    }
}

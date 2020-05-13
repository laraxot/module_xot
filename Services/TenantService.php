<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Str;

class TenantService {
    public static function getName($params = []) {
        $default = 'localhost';
        if (! isset($_SERVER['SERVER_NAME']) || '127.0.0.1' == $_SERVER['SERVER_NAME']) {
            $_SERVER['SERVER_NAME'] = $default;
        }
        $server_name = Str::slug(\str_replace('www.', '', $_SERVER['SERVER_NAME']));
        if (! file_exists(base_path('config/'.$server_name))) {
            $server_name = $default;
        }

        return $server_name;
    }

    //end function

    public static function filePath($filename) {
        $path = base_path('config/'.self::getName().'/'.$filename);
        $path = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);

        return $path;
    }

    //end function

    public static function config($key) {
        $group = implode('.', array_slice(explode('.', $key), 0, 2));
        if (in_admin() && Str::startsWith($key, 'xra.model')) {
            $module_name = \Request::segment(2);
            $models = getModuleModels($module_name);
            $original_conf = config('xra.model');
            if (! is_array($original_conf)) {
                $original_conf = [];
            }
            $merge_conf = array_merge($original_conf, $models);
            \Config::set('xra.model', $merge_conf);
        }
        $tenant_name = self::getName();
        $extra_conf = config($tenant_name.'.'.$group);
        $original_conf = config($group);
        //ddd($extra_conf);
        if (! is_array($original_conf)) {
            $original_conf = [];
        }
        if (! is_array($extra_conf)) {
            $extra_conf = [];
        }
        $merge_conf = array_merge($original_conf, $extra_conf); //_recursive
        \Config::set($group, $merge_conf);  // non so se metterlo ..
        return config($key);
    }
}

<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

/**
 * Class FileService.
 */
class FileService {
    /**
     * @return false|mixed|string
     */
    public static function asset(string $path) {
        if ('/' == $path[0]) {
            $path = \mb_substr($path, 1);
        }
        $str = 'theme/bc/';
        if (Str::startsWith($path, $str)) {
            return asset('/bc/'.\mb_substr($path, \mb_strlen($str)));
        }
        $str = 'theme/pub/';
        if (Str::startsWith($path, $str)) {
            $path = 'pub_theme::'.\mb_substr($path, \mb_strlen($str));
        }
        $str = 'theme/';
        if (Str::startsWith($path, $str)) {
            $path = 'adm_theme::'.\mb_substr($path, \mb_strlen($str));
        }

        $str = 'https://';
        if (Str::startsWith($path, $str)) {
            return $path;
        }

        $str = 'http://';
        if (Str::startsWith($path, $str)) {
            return $path;
        }

        if (! Str::contains($path, '::')) {
            return $path;
        }

        /*
            to DOOOO
            viewNamespaceToPath     => /images/prova.png
            viewNamespaceToDir      => c:\var\wwww\test\images\prova.png
            viewNamespaceToAsset    => http://example.com/images/prova.png
        */
        //dddx(\Module::asset('blog:img/logo.img')); //localhost/modules/blog/img/logo.img

        return asset(self::viewNamespaceToAsset($path));
    }

    /**
     * @return string|string[]
     */
    public static function viewNamespaceToDir(string $view) {
        //$pos = \mb_strpos($view, '::');
        //$pack = \mb_substr($view, 0, $pos);
        $ns = Str::before($view, '::');
        //$relative_path = \str_replace('.', '/', \mb_substr($view, $pos + 2));
        $relative_path = \str_replace('.', '/', Str::after($view, '::'));

        /*
        $viewHints = View::getFinder()->getHints();
        $pack_dir = $viewHints[$pack][0];
        */
        $pack_dir = self::getViewNameSpacePath($ns);
        $view_dir = $pack_dir.'/'.$relative_path;
        $view_dir = \str_replace('/', \DIRECTORY_SEPARATOR, $view_dir);

        return $view_dir;
    }

    /**
     * @return string
     */
    public static function getViewNameSpacePath(string $ns): ?string {
        if (null == $ns) {
            return null;
        }
        //dirname(\View::getFinder()->find('theme::includes.components.form.text'));
        //View::getFinder()
        // View finder.
        $finder = view()->getFinder();
        $viewHints = [];
        if (method_exists($finder, 'getHints')) {
            $viewHints = $finder->getHints();
        }
        if (isset($viewHints[$ns])) {
            return $viewHints[$ns][0];
        }

        return null;
    }

    public static function getViewNameSpaceUrl(string $ns, string $path1): string {
        if (in_array($ns, ['pub_theme', 'adm_theme'])) {
            $path = self::getViewNameSpacePath($ns);
        } else {
            $module_path = \Module::getModulePath($ns);
            $view_dir = config('modules.paths.generator.views.path');
            $path = $module_path.$view_dir;
        }

        $filename = $path.DIRECTORY_SEPARATOR.$path1;
        $public_path = realpath(public_path('/'));
        if (false === $public_path) {
            throw new Exception('do not reach public path');
        }

        if (Str::startsWith($filename, $public_path)) {
            $url = substr($filename, strlen($public_path));
            $url = str_replace(DIRECTORY_SEPARATOR, '/', $url);

            return asset($url);
        }
        /* 4 debug , dovrebbe uscire al return prima
        if($ns=='adm_theme'){

            ddd($msg);
        }
        //*/
        $url = Module::asset($ns.':'.$path1);
        $filename_pub = \Module::assetPath($ns).DIRECTORY_SEPARATOR.$path1;
        if (! File::exists(\dirname($filename_pub))) {
            try {
                File::makeDirectory(\dirname($filename_pub), 0755, true, true);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (File::exists($filename)) {
            try {
                //echo '<hr>'.$filename.' >>>>  '.$filename_pub; //4 debug
                File::copy($filename, $filename_pub);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        } else {
            $msg = [
                'ns' => $ns,
                'path1' => $path1,
                'filename' => $filename,
                'msg' => 'Filename not Exists',
            ];
            dddx($msg); //4 debug
        }

        //$url=str_replace(url('/'),'',$url);
        //ddd(url($url));
        return $url;
    }

    public static function getViewNameSpaceUrl_nomodule(string $ns, string $path1): string {
        $path = (string) self::getViewNameSpacePath($ns);
        /* 4 debug
        if(basename($path1)=='font-awesome.min.css'){
            ddd('-['.$path.']['.public_path('').']');
        }
        //*/
        if (Str::startsWith($path, public_path(''))) {
            $relative = \mb_substr($path, \mb_strlen(public_path('')));
            $relative = str_replace('\\', '/', $relative);

            return asset($relative.'/'.$path1);
        }
        $filename = $path.'/'.$path1;
        $path_pub = 'assets_packs/'.$ns.'/'.$path1;
        $filename_pub = public_path($path_pub);

        if (! \File::exists(\dirname($filename_pub))) {
            try {
                \File::makeDirectory(\dirname($filename_pub), 0755, true, true);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (\File::exists($filename)) {
            try {
                //echo '<hr>'.$filename.' >>>>  '.$filename_pub; //4 debug
                \File::copy($filename, $filename_pub);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        } else {
            $filename = str_replace('/', DIRECTORY_SEPARATOR, $filename);
            $full = $ns.':'.$path1;
            $msg = [
                'ns' => $ns,
                'Module::getModulePath' => \Module::getModulePath($ns.':'.$path1), ///home/vagrant/code/htdocs/lara/foodm/Modules/LU/
                'Module::assetPath' => \Module::assetPath($ns), //"/home/vagrant/code/htdocs/lara/foodm/public/modules/lu
                'view_dir' => config('modules.paths.generator.views.path'),
                'full' => $full,
                'test1' => \Module::asset($full),
                'test2' => \Module::getAssetsPath(),
                'path1' => $path1,
                'filename' => $filename,
                'msg' => 'Filename not Exists',
            ];
            dddx($msg);
            //ddd('non esiste '.); //4 debug
        }

        return asset($path_pub);
    }

    public static function path2Url(string $path, string $ns): string {
        if (Str::startsWith($path, public_path('/'))) {
            $relative = \mb_substr($path, \mb_strlen(public_path('/')));

            return asset($relative);
        }
        $filename = $path;
        $ns_dir = (string) self::getViewNameSpacePath($ns);
        $path1 = substr($path, strlen($ns_dir));
        $path_pub = 'assets_packs/'.$ns.'/'.$path1;
        $filename_pub = public_path($path_pub);

        if (! \File::exists(\dirname($filename_pub))) {
            try {
                \File::makeDirectory(\dirname($filename_pub), 0755, true, true);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (\File::exists($filename)) {
            try {
                //echo '<hr>'.$filename.' >>>>  '.$filename_pub; //4 debug
                \File::copy($filename, $filename_pub);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        } else {
            //ddd('non esiste '.$filename); //4 debug
        }

        return asset($path_pub);
    }

    public static function viewThemeNamespaceToAsset(string $key): string {
        $ns_name = Str::before($key, '::');
        //$ns_dir = View::getFinder()->getHints()[$ns_name][0];
        $ns_dir = self::getViewNameSpacePath($ns_name);
        $ns_name = config('xra.'.$ns_name);
        $tmp = Str::after($key, '::');
        $tmp0 = Str::before($tmp, '/');
        $tmp1 = Str::after($tmp, '/');
        //--------------------------------------------------
        $filename = str_replace('.', '/', $tmp0).'/'.$tmp1;
        $filename_from = $ns_dir.'/'.$filename;
        $asset = '/themes/'.$ns_name.'/'.$filename;
        $filename_to = public_path($asset);
        $filename_from = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $filename_from);
        //--------------------------------------------------
        $msg = [
            'filename' => $filename,
            'from' => $filename_from,
            'to' => $filename_to,
            'asset' => $asset,
            'pub_theme' => config('xra.pub_theme'),
        ];

        $dir_to = \dirname($filename_to);
        if (! \File::exists($dir_to)) {
            try {
                File::makeDirectory($dir_to, 0755, true, true);
            } catch (Exception $e) {
                dddx(['Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']']);
            }
        }

        if (! File::exists($filename_from)) {
            //dddx('['.$filename_from.'] not exists');
            //ddd($msg);
            return '['.$filename_from.']['.__LINE__.']['.__FILE__.'] not exists';
        }
        if (! File::exists($filename_to)) {
            try {
                File::copy($filename_from, $filename_to);
            } catch (Exception $e) {
                dddx(['Caught exception: '.$e->getMessage()]);
            }
        }

        return $asset;
    }

    public static function viewNamespaceToAsset(string $key): string {
        $ns_name = Str::before($key, '::');

        //$ns_dir = View::getFinder()->getHints()[$ns_name][0];
        /*
        $ns_dir = collect(View::getFinder()->getHints())->filter(function ($item, $key) use ($ns_name) {
            return $key == $ns_name;
        })->collapse()->first();
        */
        $ns_dir = self::getViewNameSpacePath($ns_name);
        if (null == $ns_dir) {
            return '#['.$key.']['.__LINE__.']['.__FILE__.']';
        }
        //dddx([$key, $ns_name, $ns_dir, $ns_dir1]);
        $tmp = Str::after($key, '::');
        $tmp0 = Str::before($tmp, '/');
        $tmp1 = Str::after($tmp, '/');

        $filename = str_replace('.', '/', $tmp0).'/'.$tmp1;
        $filename_from = $ns_dir.'/'.$filename;
        $filename_from = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $filename_from);
        $public_path = public_path();

        if (Str::startsWith($filename_from, $public_path)) {  //se e' in un percoro navigabile
            $path = Str::after($filename_from, $public_path);
            $path = str_replace(['\\'], ['/'], $path);

            return asset($path);
        }

        if (in_array($ns_name, ['pub_theme', 'adm_theme'])) {
            $asset = self::viewThemeNamespaceToAsset($key);

            return $asset;
        }

        $tmp = 'assets/'.$ns_name.'/'.$filename;
        $filename_to = public_path($tmp);
        $filename_to = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $filename_to);
        $asset = asset($tmp);
        $msg = [
            'key' => $key,
            'filename_from' => $filename_from,
            'filename_from_exists' => File::exists($filename_from),
            'filename_to' => $filename_to,
            'filename_to_exists' => File::exists($filename_to),
            'asset' => $asset,
            'public_path' => public_path(),
        ];
        if (! File::exists($filename_from)) {
        }
        $dir_to = \dirname($filename_to);
        if (! \File::exists($dir_to)) {
            try {
                File::makeDirectory($dir_to, 0755, true, true);
            } catch (Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        //*
        if (File::exists($filename_to)) {
            File::delete($filename_to); //
            //return $asset;
        }
        //*/
        if (File::exists($filename_from) && ! File::exists($filename_to)) {
            try {
                File::copy($filename_from, $filename_to);
            } catch (Exception $e) {
                dddx(['Caught exception: '.$e->getMessage()]);
            }
        } else {
            //if (! File::exists($filename_from)) {
            //    dddx('['.$filename_from.'] not exists');
            //}
        }

        return $asset;
    }

    /*
    public static function url($path)
    {
       if($path=='') return $path;
       if ('/' == $path[0]) {
           $path = \mb_substr($path, 1);
       }
       $str = 'theme/bc/';
       if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
           $filename = asset('/bc/'.\mb_substr($path, \mb_strlen($str)));

           return $filename;
       }
       $str = 'theme/pub/';
       $theme = config('xra.pub_theme');
       if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
           $filename = asset('/themes/'.$theme.'/'.\mb_substr($path, \mb_strlen($str)));

           return $filename;
       }
       $str = 'theme/';
       $theme = config('xra.adm_theme');
       if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
           $filename = asset('/themes/'.$theme.'/'.\mb_substr($path, \mb_strlen($str)));

           return $filename;
       }

       return ''.$path;
    }
    */
    //*

    public static function getFileUrl(string $path): string {
        if (Str::startsWith($path, '//')) {
        } elseif (Str::startsWith($path, '/')) {
            $path = \mb_substr($path, 1);
        }
        $str = 'theme/bc/';
        if (Str::startsWith($path, $str)) {
            $filename = asset('/bc/'.\mb_substr($path, \mb_strlen($str)));

            return $filename;
        }
        $str = 'theme/pub/';
        $theme = config('xra.pub_theme');
        if (Str::startsWith($path, $str)) {
            $filename = asset('/themes/'.$theme.'/'.\mb_substr($path, \mb_strlen($str)));

            return $filename;
        }
        $str = 'theme/';
        $theme = config('xra.adm_theme');
        if (Str::startsWith($path, $str)) {
            $filename = asset('/themes/'.$theme.'/'.\mb_substr($path, \mb_strlen($str)));

            return $filename;
        }

        return ''.$path;
    }

    //*/
    //*

    /**
     * @param mixed[] $files
     *
     * @return mixed
     */
    public static function viewNamespaceToUrl($files) {
        foreach ($files as $k => $filePath) {
            //TODO testare con ARTISAN vendor:publish
            $pos = \mb_strpos($filePath, '::');
            if ($pos) {
                $hints = \mb_substr($filePath, 0, $pos);
                $filename = \mb_substr($filePath, $pos + 2);
                $viewNamespace = (string) self::getViewNameSpacePath($hints);
                /*
                $viewHints = View::getFinder()->getHints();
                if (isset($viewHints[$hints][0])) {
                    $viewNamespace = $viewHints[$hints][0];
                } else {
                    $viewNamespace = '---';
                }
                */
                if ('pub_theme' == $hints) {
                    $tmp = \str_replace(public_path(''), '', $viewNamespace);
                    $tmp = \str_replace(\DIRECTORY_SEPARATOR, '/', $tmp);
                    $pos = \mb_strpos($filename, '/');
                    if (false === $pos) {
                        throw new Exception('not found / on filename');
                    }
                    $filename0 = \mb_substr($filename, 0, $pos);
                    $filename0 = \str_replace('.', '/', $filename0);
                    $filename1 = \mb_substr($filename, $pos);
                    $filename = $filename0.''.$filename1;
                    //echo '<h3>'.$filename0.''.$filename1.'</h3>';
                    //dd($tmp.'/'.$filename);
                    $new_url = $tmp.'/'.$filename;
                } else {
                    $old_path = $viewNamespace.\DIRECTORY_SEPARATOR.$filename;
                    $old_path = \str_replace('/', \DIRECTORY_SEPARATOR, $old_path);
                    $new_path = public_path('assets_packs'.\DIRECTORY_SEPARATOR.$hints.\DIRECTORY_SEPARATOR.$filename);
                    $new_path = \str_replace('/', \DIRECTORY_SEPARATOR, $new_path);
                    if (! \File::exists(\dirname($new_path))) {
                        try {
                            \File::makeDirectory(\dirname($new_path), 0755, true, true);
                        } catch (Exception $e) {
                            dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
                        }
                    }
                    if (\File::exists($old_path)) {
                        try {
                            \File::copy($old_path, $new_path);
                        } catch (Exception $e) {
                            dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
                        }
                    }
                    $new_url = \str_replace(public_path(), '', $new_path);
                    $new_url = \str_replace('\\/', '/', $new_url);
                    $new_url = \str_replace(\DIRECTORY_SEPARATOR, '/', $new_url);
                }
                $files[$k] = $new_url;
            }
        }

        return $files;
    }

    //*/

    public static function getRealFile(string $path): string {
        $filename = '';
        if (Str::startsWith($path, asset(''))) {
            return public_path(substr($path, strlen(asset(''))));
        }
        if ('/' == $path[0]) {
            $path = \mb_substr($path, 1);
        }
        $str = 'theme/bc/';
        //if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
        if (Str::startsWith($path, $str)) {
            $filename = public_path('bc/'.\mb_substr($path, \mb_strlen($str)));
            //$filename=str_replace('\\/','/',$filename);
            //$filename=realpath($filename);
            return $filename;
        }
        $str = 'theme/pub/';
        $theme = config('xra.pub_theme');
        //if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
        if (Str::startsWith($path, $str)) {
            $filename = public_path('themes/'.$theme.'/'.\mb_substr($path, \mb_strlen($str)));
            //$filename=str_replace('\\/','/',$filename);
            //$filename=realpath($filename);
            return $filename;
        }
        $str = 'theme/';
        $theme = config('xra.adm_theme');
        //if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
        if (Str::startsWith($path, $str)) {
            $filename = public_path('themes/'.$theme.'/'.\mb_substr($path, \mb_strlen($str)));

            return $filename;
        }
        $str = 'https://';
        if (Str::startsWith($path, $str)) {
            $info = pathinfo($path);
            switch (collect($info)->get('extension')) {
                case 'css': $filename = public_path('/css/'.$info['basename']); break;
                case 'js':  $filename = public_path('/js/'.$info['basename']); break;
                default:
                    echo '<h3>Unknown Extension</h3>';
                    echo '<h3>['.$path.']</h3>';
                    dddx($info);
                break;
            }
            ImportService::download(['url' => $path, 'filename' => $filename]);

            return $filename;
        }

        return ''.$path;
    }
}
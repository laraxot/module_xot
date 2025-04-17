<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use const DIRECTORY_SEPARATOR;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\realpath;
use function strlen;

use Webmozart\Assert\Assert;

/**
 * Class FileService.
 */
class FileService
{
    /**
     * 18     Method Modules\Xot\Services\FileService::asset() should return string but return statement is missing.
     */
    public static function asset(string $path): string
    {
        /*
            to DOOOO
            viewNamespaceToPath     => /images/prova.png
            viewNamespaceToDir      => c:\var\wwww\test\images\prova.png
            viewNamespaceToAsset    => http://example.com/images/prova.png
        */
        // dddx(\Module::asset('blog:img/logo.img')); //localhost/modules/blog/img/logo.img

        if (Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (Str::startsWith($path, 'http://')) {
            return $path;
        }

        if (File::exists(public_path($path))) {
            return $path;
        }

        if (Str::startsWith($path, '/theme/pub')) {
            $path = 'pub_theme::'.Str::after($path, '/theme/pub');
        }

        if (Str::startsWith($path, 'theme/pub')) {
            $path = 'pub_theme::'.Str::after($path, 'theme/pub');
        }

        $ns = Str::before($path, '::');
        $ns_after = Str::after($path, '::');
        if ($ns === $path) {
            $ns = inAdmin() ? 'adm_theme' : 'pub_theme';
        }

        $ns_after0 = Str::before($ns_after, '/');
        $ns_after1 = Str::after($ns_after, '/');
        $ns_after = str_replace('.', '/', $ns_after0).'/'.$ns_after1;

        if (Str::startsWith($ns_after, '/')) {
            $ns_after = Str::after($ns_after, '/');
        }
        if (\in_array($ns, ['pub_theme', 'adm_theme'], true)) {
            $theme = config('xra.'.$ns);

            $filename_from = self::fixPath(base_path('Themes/'.$theme.'/Resources/'.$ns_after));
            // $filename_from = Str::replace('/Resources//', '/Resources/', $filename_from);
            $asset = 'themes/'.$theme.'/'.$ns_after;
            $filename_to = self::fixPath(public_path($asset));
            $asset = Str::replace(url(''), '', asset($asset));

            if (! File::exists($filename_to)) {
                if (! File::exists(\dirname($filename_to))) {
                    File::makeDirectory(\dirname($filename_to), 0755, true, true);
                }
                try {
                    File::copy($filename_from, $filename_to);
                } catch (\Exception $e) {
                    throw new \Exception('message:['.$e->getMessage().']
                        path :['.$path.']
                        file from ['.$filename_from.']
                        file to ['.$filename_to.']');
                }
            }
            Assert::string($asset, 'wip');

            return $asset;
        }

        $module_path = Module::getModulePath($ns);
        if (Str::endsWith($module_path, '/')) {
            $module_path = Str::beforeLast($module_path, '/');
        }
        $filename_from = self::fixPath($module_path.'/Resources/'.$ns_after);
        $asset = 'assets/'.$ns.'/'.$ns_after;
        $filename_to = self::fixPath(public_path($asset));
        $asset = Str::replace(url(''), '', asset($asset));
        if (! File::exists($filename_from)) {
            throw new \Exception('file ['.$filename_from.'] not Exists , path ['.$path.']');
        }

        // dddx(app()->environment());// local
        if (! File::exists($filename_to) || 'production' !== app()->environment()) {
            if (! File::exists(\dirname($filename_to))) {
                File::makeDirectory(\dirname($filename_to), 0755, true, true);
            }
            // 105    If condition is always true.
            // if (File::exists($filename_from)) {
            File::copy($filename_from, $filename_to);
            // }
        }
        Assert::string($asset, 'wip');

        return $asset;

        // return asset(self::viewNamespaceToAsset($path));
    }

    /**
     * Undocumented function.
     *
     * @return void
     */
    public static function createDirectoryForFilename(string $filename)
    {
        if (! File::exists(\dirname($filename))) {
            File::makeDirectory(\dirname($filename), 0755, true, true);
        }
    }

    /**
     * @return string|string[]
     */
    public static function viewNamespaceToDir(string $view)
    {
        $ns = Str::before($view, '::');
        // dddx(Str::after($view, '::'));
        $relative_path = str_replace('.', '/', Str::after($view, '::'));
        $pack_dir = self::getViewNameSpacePath($ns);
        $view_dir = $pack_dir.'/'.$relative_path;
        $view_dir = str_replace('/', \DIRECTORY_SEPARATOR, $view_dir);

        return $view_dir;
    }

    /**
     * @return string
     */
    public static function getViewNameSpacePath(string $ns): ?string
    {
        $finder = view()->getFinder();
        $viewHints = [];
        if (method_exists($finder, 'getHints')) {
            $viewHints = $finder->getHints();
        }
        if (isset($viewHints[$ns])) {
            return $viewHints[$ns][0];
        }

        if (\in_array($ns, ['pub_theme', 'adm_theme'], true)) {
            $theme_name = config('xra.'.$ns);

            return base_path('Themes/'.$theme_name);
        }

        return null;
    }

    public static function assetPath(string $asset): string
    {
        /*
        $resolver=app(NamespacedItemResolver::class);
        dddx($resolver->parseKey($asset));
        da 'notify::css/ark.css'
        ret  0 => "notify"
    1 => "css/ark"
    2 => "css"
        */
        // ---------- WIP -----------
        // dddx(get_class_methods(app()));
        // dddx(Storage::disk('notify'));
        // dddx(Module::assetPath('notify')); ///var/www/html/ptvx/public_html/modules/notify
        // $module=Module::find('notify');
        // dddx([get_class_methods($module),$module->getPath()]);
        [$ns,$file] = explode('::', $asset);
        $module_path = Module::getModulePath($ns).'Resources';
        $path = $module_path.'/'.$file;

        return $path;
    }

    public static function getViewNameSpaceUrl(string $ns, string $path1): string
    {
        if (\in_array($ns, ['pub_theme', 'adm_theme'], true)) {
            $path = self::getViewNameSpacePath($ns);
        } else {
            $module_path = Module::getModulePath($ns);
            $view_dir = config('modules.paths.generator.views.path');
            $path = $module_path.$view_dir;
        }

        $filename = $path.\DIRECTORY_SEPARATOR.$path1;
        $public_path = realpath(public_path('/'));
        // if (false === $public_path) {
        //    throw new \Exception('do not reach public path');
        // }

        if (Str::startsWith($filename, $public_path)) {
            $url = substr($filename, \strlen($public_path));
            $url = str_replace(\DIRECTORY_SEPARATOR, '/', $url);

            return asset($url);
        }
        /* 4 debug , dovrebbe uscire al return prima
        if($ns=='adm_theme'){

            dddx($msg);
        }
        //*/
        $url = Module::asset($ns.':'.$path1);
        $filename_pub = Module::assetPath($ns).\DIRECTORY_SEPARATOR.$path1;
        if (! File::exists(\dirname($filename_pub))) {
            try {
                File::makeDirectory(\dirname($filename_pub), 0755, true, true);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (File::exists($filename)) {
            try {
                // echo '<hr>'.$filename.' >>>>  '.$filename_pub; //4 debug
                File::copy($filename, $filename_pub);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        } else {
            $msg = [
                'ns' => $ns,
                'path1' => $path1,
                'filename' => $filename,
                'msg' => 'Filename not Exists',
            ];
            dddx($msg); // 4 debug
        }

        // $url=str_replace(url('/'),'',$url);
        // dddx(url($url));
        return $url;
    }

    public static function getViewNameSpaceUrl_nomodule(string $ns, string $path1): string
    {
        $path = (string) self::getViewNameSpacePath($ns);
        /* 4 debug
        if(basename($path1)=='font-awesome.min.css'){
            dddx('-['.$path.']['.public_path('').']');
        }
        //*/
        if (Str::startsWith($path, public_path(''))) {
            $relative = mb_substr($path, mb_strlen(public_path('')));
            $relative = str_replace('\\', '/', $relative);

            return asset($relative.'/'.$path1);
        }
        $filename = $path.'/'.$path1;
        $path_pub = 'assets_packs/'.$ns.'/'.$path1;
        $filename_pub = public_path($path_pub);

        if (! \File::exists(\dirname($filename_pub))) {
            try {
                \File::makeDirectory(\dirname($filename_pub), 0755, true, true);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (\File::exists($filename)) {
            try {
                // echo '<hr>'.$filename.' >>>>  '.$filename_pub; //4 debug
                \File::copy($filename, $filename_pub);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        } else {
            $filename = str_replace('/', \DIRECTORY_SEPARATOR, $filename);
            $full = $ns.':'.$path1;
            $msg = [
                'ns' => $ns,
                'Module::getModulePath' => Module::getModulePath($ns.':'.$path1), // /home/vagrant/code/htdocs/lara/foodm/Modules/LU/
                'Module::assetPath' => Module::assetPath($ns), // "/home/vagrant/code/htdocs/lara/foodm/public/modules/lu
                'view_dir' => config('modules.paths.generator.views.path'),
                'full' => $full,
                'test1' => Module::asset($full),
                'test2' => Module::getAssetsPath(),
                'path1' => $path1,
                'filename' => $filename,
                'msg' => 'Filename not Exists',
            ];
            dddx($msg);
            // dddx('non esiste '.); //4 debug
        }

        return asset($path_pub);
    }

    public static function path2Url(string $path, string $ns): string
    {
        if (Str::startsWith($path, public_path('/'))) {
            $relative = mb_substr($path, mb_strlen(public_path('/')));

            return asset($relative);
        }
        $filename = $path;
        $ns_dir = (string) self::getViewNameSpacePath($ns);
        $path1 = substr($path, \strlen($ns_dir));
        $path_pub = 'assets_packs/'.$ns.'/'.$path1;
        $filename_pub = public_path($path_pub);

        if (! \File::exists(\dirname($filename_pub))) {
            try {
                \File::makeDirectory(\dirname($filename_pub), 0755, true, true);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        if (\File::exists($filename)) {
            try {
                // echo '<hr>'.$filename.' >>>>  '.$filename_pub; //4 debug
                \File::copy($filename, $filename_pub);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        } else {
            // dddx('non esiste '.$filename); //4 debug
        }

        return asset($path_pub);
    }

    public static function viewThemeNamespaceToAsset(string $key): string
    {
        $ns_name = Str::before($key, '::');
        // $ns_dir = View::getFinder()->getHints()[$ns_name][0];
        $ns_dir = self::getViewNameSpacePath($ns_name);
        $ns_name = config('xra.'.$ns_name);
        $tmp = Str::after($key, '::');
        $tmp0 = Str::before($tmp, '/');
        $tmp1 = Str::after($tmp, '/');
        // --------------------------------------------------
        $filename = str_replace('.', '/', $tmp0).'/'.$tmp1;
        $filename_from = $ns_dir.'/'.$filename;
        $asset = '/themes/'.$ns_name.'/'.$filename;
        $filename_to = public_path($asset);
        $filename_from = str_replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $filename_from);
        // --------------------------------------------------
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
            } catch (\Exception $e) {
                dddx(['Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']']);
            }
        }

        if (! File::exists($filename_from)) {
            // dddx('['.$filename_from.'] not exists');
            // dddx($msg);
            return '['.$filename_from.']['.__LINE__.']['.basename(__FILE__).'] not exists';
        }
        if (! File::exists($filename_to)) {
            try {
                File::copy($filename_from, $filename_to);
            } catch (\Exception $e) {
                dddx(['Caught exception: '.$e->getMessage()]);
            }
        }

        return $asset;
    }

    public static function viewNamespaceToAsset(string $key): string
    {
        $ns_name = Str::before($key, '::');

        // $ns_dir = View::getFinder()->getHints()[$ns_name][0];
        /*
        $ns_dir = collect(View::getFinder()->getHints())->filter(function ($item, $key) use ($ns_name) {
            return $key == $ns_name;
        })->collapse()->first();
        */
        $ns_dir = self::getViewNameSpacePath($ns_name);
        if (null === $ns_dir) {
            return '#['.$key.']['.__LINE__.']['.__FILE__.']';
        }
        // dddx([$key, $ns_name, $ns_dir, $ns_dir1]);
        $tmp = Str::after($key, '::');
        $tmp0 = Str::before($tmp, '/');
        $tmp1 = Str::after($tmp, '/');

        $filename = str_replace('.', '/', $tmp0).'/'.$tmp1;
        $filename_from = $ns_dir.'/'.$filename;
        $filename_from = str_replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $filename_from);
        $public_path = public_path();

        if (Str::startsWith($filename_from, $public_path)) {  // se e' in un percoro navigabile
            $path = Str::after($filename_from, $public_path);
            $path = str_replace(['\\'], ['/'], $path);

            return asset($path);
        }

        if (\in_array($ns_name, ['pub_theme', 'adm_theme'], true)) {
            $asset = self::viewThemeNamespaceToAsset($key);

            return $asset;
        }

        $tmp = 'assets/'.$ns_name.'/'.$filename;
        $filename_to = public_path($tmp);
        $filename_to = str_replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $filename_to);
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
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }
        // *
        if (File::exists($filename_to)) {
            File::delete($filename_to); //
            // return $asset;
        }
        // */
        if (File::exists($filename_from) && ! File::exists($filename_to)) {
            try {
                File::copy($filename_from, $filename_to);
            } catch (\Exception $e) {
                dddx(
                    [
                        'message' => $e->getMessage(),
                        'filename_from' => $filename_from,
                        'filename_to' => $filename_to,
                    ]
                );
            }
        } else {
            // if (! File::exists($filename_from)) {
            //    dddx('['.$filename_from.'] not exists');
            // }
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
    // *

    public static function getFileUrl(string $path): string
    {
        if (Str::startsWith($path, '//')) {
        } elseif (Str::startsWith($path, '/')) {
            $path = mb_substr($path, 1);
        }
        $str = 'theme/bc/';
        if (Str::startsWith($path, $str)) {
            $filename = asset('/bc/'.mb_substr($path, mb_strlen($str)));

            return $filename;
        }
        $str = 'theme/pub/';
        $theme = config('xra.pub_theme');
        if (Str::startsWith($path, $str)) {
            $filename = asset('/themes/'.$theme.'/'.mb_substr($path, mb_strlen($str)));

            return $filename;
        }
        $str = 'theme/';
        $theme = config('xra.adm_theme');
        if (Str::startsWith($path, $str)) {
            $filename = asset('/themes/'.$theme.'/'.mb_substr($path, mb_strlen($str)));

            return $filename;
        }

        return ''.$path;
    }

    // */
    // *

    /**
     * @param string[] $files
     */
    public static function viewNamespaceToUrl($files): array
    {
        foreach ($files as $k => $filePath) {
            // TODO testare con ARTISAN vendor:publish
            $pos = mb_strpos($filePath, '::');
            if ($pos) {
                $hints = mb_substr($filePath, 0, $pos);
                $filename = mb_substr($filePath, $pos + 2);
                $viewNamespace = (string) self::getViewNameSpacePath($hints);
                /*
                $viewHints = View::getFinder()->getHints();
                if (isset($viewHints[$hints][0])) {
                    $viewNamespace = $viewHints[$hints][0];
                } else {
                    $viewNamespace = '---';
                }
                */
                if ('pub_theme' === $hints) {
                    $tmp = str_replace(public_path(''), '', $viewNamespace);
                    $tmp = str_replace(\DIRECTORY_SEPARATOR, '/', $tmp);
                    $pos = mb_strpos($filename, '/');
                    if (false === $pos) {
                        throw new \Exception('not found / on filename');
                    }
                    $filename0 = mb_substr($filename, 0, $pos);
                    $filename0 = str_replace('.', '/', $filename0);
                    $filename1 = mb_substr($filename, $pos);
                    $filename = $filename0.''.$filename1;
                    // echo '<h3>'.$filename0.''.$filename1.'</h3>';
                    // dd($tmp.'/'.$filename);
                    $new_url = $tmp.'/'.$filename;
                } else {
                    $old_path = $viewNamespace.\DIRECTORY_SEPARATOR.$filename;
                    $old_path = str_replace('/', \DIRECTORY_SEPARATOR, $old_path);
                    $new_path = public_path('assets_packs'.\DIRECTORY_SEPARATOR.$hints.\DIRECTORY_SEPARATOR.$filename);
                    $new_path = str_replace('/', \DIRECTORY_SEPARATOR, $new_path);
                    if (! \File::exists(\dirname($new_path))) {
                        try {
                            \File::makeDirectory(\dirname($new_path), 0755, true, true);
                        } catch (\Exception $e) {
                            dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
                        }
                    }
                    if (\File::exists($old_path)) {
                        try {
                            \File::copy($old_path, $new_path);
                        } catch (\Exception $e) {
                            dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
                        }
                    }
                    $new_url = str_replace(public_path(), '', $new_path);
                    $new_url = str_replace('\\/', '/', $new_url);
                    $new_url = str_replace(\DIRECTORY_SEPARATOR, '/', $new_url);
                }
                $files[$k] = $new_url;
            }
        }

        return $files;
    }

    // */

    public static function getRealFile(string $path): string
    {
        $filename = '';
        if (Str::startsWith($path, asset(''))) {
            return public_path(substr($path, \strlen(asset(''))));
        }
        if ('/' === $path[0]) {
            $path = mb_substr($path, 1);
        }
        $str = 'theme/bc/';
        // if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
        if (Str::startsWith($path, $str)) {
            $filename = public_path('bc/'.mb_substr($path, mb_strlen($str)));
            // $filename=str_replace('\\/','/',$filename);
            // $filename=realpath($filename);
            return $filename;
        }
        $str = 'theme/pub/';
        $theme = config('xra.pub_theme');
        // if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
        if (Str::startsWith($path, $str)) {
            $filename = public_path('themes/'.$theme.'/'.mb_substr($path, mb_strlen($str)));
            // $filename=str_replace('\\/','/',$filename);
            // $filename=realpath($filename);
            return $filename;
        }
        $str = 'theme/';
        $theme = config('xra.adm_theme');
        // if (\mb_substr($path, 0, \mb_strlen($str)) == $str) {
        if (Str::startsWith($path, $str)) {
            $filename = public_path('themes/'.$theme.'/'.mb_substr($path, mb_strlen($str)));

            return $filename;
        }
        $str = 'https://';
        if (Str::startsWith($path, $str)) {
            $info = pathinfo($path);
            switch (collect($info)->get('extension')) {
                case 'css': $filename = public_path('/css/'.$info['basename']);
                    break;
                case 'js':  $filename = public_path('/js/'.$info['basename']);
                    break;
                default:
                    echo '<h3>Unknown Extension</h3>';
                    echo '<h3>['.$path.']</h3>';
                    dddx($info);
                    break;
            }
            ImportService::make()->download(['url' => $path, 'filename' => $filename]);

            return $filename;
        }

        return ''.$path;
    }

    public static function allDirectories(string $path, array $except = [], string $dir = ''): array
    {
        $dirs = File::directories($path);
        $data = [];
        foreach ($dirs as $v) {
            $name = Str::after($v, $path.\DIRECTORY_SEPARATOR);
            $value = '' === $dir ? $name : $dir.\DIRECTORY_SEPARATOR.$name;
            if (! \in_array($name, $except, true)) {
                $data[] = $value;
                $sub = self::allDirectories($v, $except, $value);
                if ([] !== $sub) {
                    $data = array_merge($data, $sub);
                }
            }
        }

        return $data;
    }

    public static function fixPath(string $path): string
    {
        $path = str_replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $path);

        return $path;
    }

    /**
     * Undocumented function.
     *
     * @return int|float|string|array|null
     */
    public static function config(string $key)
    {
        $ns_name = Str::before($key, '::');
        $group = Str::of($key)->after('::')->before('.');
        $item = Str::after($key, $ns_name.'::'.$group.'.');
        $ns_dir = self::getViewNameSpacePath($ns_name);
        $path = $ns_dir.'/../../Config/'.$group.'.php';
        if (! File::exists($path)) {
            ArrayService::save(['filename' => $path, 'data' => []]);
        }
        $data = File::getRequire($path);
        if (! \is_array($data)) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }
        $value = Arr::get($data, $item);

        if ($item === $key) {
            return $data;
        }

        if (! is_numeric($value) && ! \is_array($value) && ! \is_string($value) && null !== $value) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        return $value;
    }

    public static function viewPath(string $key): string
    {
        $ns_name = Str::before($key, '::');
        /**
         * @var iterable<string>|string
         */
        $group = Str::of($key)->after('::');
        $ns_dir = self::getViewNameSpacePath($ns_name);
        Assert::string($group_dir = Str::replace('.', '/', $group), 'wip');
        $res = $ns_dir.'/'.$group_dir.'.blade.php';

        return self::fixPath($res);
    }

    public static function configPath(string $key): string
    {
        $ns_name = Str::before($key, '::');
        $group = (string) Str::of($key)->after('::')->before('.');
        $ns_dir = self::getViewNameSpacePath($ns_name);
        $res = $ns_dir.'/../../Config/'.$group.'.php';

        return self::fixPath($res);
    }

    /**
     * Undocumented function
     *  Execute copy with makedirectory.
     */
    public static function copy(string $from, string $to): void
    {
        if (! File::exists(\dirname($to))) {
            try {
                File::makeDirectory(\dirname($to), 0755, true, true);
            } catch (\Exception $e) {
                dd('Caught exception: ', $e->getMessage(), '\n['.__LINE__.']['.__FILE__.']');
            }
        }

        if (! File::exists($to) && ! app()->runningInConsole()) {// not rewite
            try {
                File::copy($from, $to);
            } catch (\Exception $e) {
                throw new \Exception('Unable to copy
                    from ['.$from.']
                    to ['.$to.']
                    message ['.$e->getMessage().']');
            }
        }
    }

    /**
     * Undocumented function.
     *
     * from : theme::errors.500
     * to  : pub_theme:errors.500
     */
    public static function viewCopy(string $from, string $to): void
    {
        $from_path = self::viewPath($from);
        $to_path = self::viewPath($to);
        self::copy($from_path, $to_path);
    }

    public static function getConfigKey(string $key): string
    {
        $ns_name = Str::before($key, '::');
        $group = Str::of($key)->after('::')->before('.');
        $key = Str::after($key, $ns_name.'::'.$group.'.');

        return $key;
    }

    /**
     * Undocumented function.
     *
     * from : theme::errors.500
     * to  : pub_theme:errors.500
     */
    public static function configCopy(string $from, string $to, bool $force = false): void
    {
        $from_path = self::configPath($from);
        $to_path = self::configPath($to);
        // self::copy($from_path, $to_path);

        $from_value = self::config($from);
        $to_value = self::config($to);

        if (null != $to_value) {
            return;
        }

        $data = File::getRequire($to_path);
        if (! \is_array($data)) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        $key = self::getConfigKey($to);

        Arr::set($data, $key, $from_value);
        /*
        dddx([
            'from_value'=>$from_value,
            'from_path'=>$from_path,
            'to_value'=>$to_value,
            'to_path'=>$to_path,
            'value'=>$value,
            'data' => $data,
        ]);
        */
        ArrayService::save(['filename' => $to_path, 'data' => $data]);
    }

    /**
     * Undocumented function.
     */
    public static function getComponents(string $path, string $namespace, string $prefix, bool $force_recreate = false): array
    {
        Assert::string($namespace = Str::replace('/', '\\', $namespace), 'wip');
        $components_json = $path.'/_components.json';
        $components_json = self::fixPath($components_json);
        $path = self::fixPath($path);
        /*
        throw new Exception ??
        if (! File::exists($path)) {
            if (Str::endsWith($path, 'Http'.DIRECTORY_SEPARATOR.'Livewire')) {
                File::makeDirectory($path, 0755, true, true);
            }
        }
        */

        $exists = File::exists($components_json);
        if ($exists && ! $force_recreate) {
            $content = File::get($components_json);
            $comps = (array) json_decode($content);
            // Strict comparison using === between null and array will always evaluate to false.
            // if (null === $comps) {
            // // File::delete($components_json);
            //    $comps = [];
            // }

            return $comps;
        }
        // $files = File::allFiles(\dirname($components_json));
        $files = File::allFiles($path);

        $comps = [];
        foreach ($files as $k => $v) {
            if ('php' === $v->getExtension()) {
                $tmp = (object) [];
                $class_name = $v->getFilenameWithoutExtension();

                $tmp->class_name = $class_name;
                Assert::string($comp_name = Str::replace('\\', ' ', $class_name), 'wip');
                $tmp->comp_name = Str::slug(Str::snake($comp_name));
                $tmp->comp_name = $prefix.$tmp->comp_name;

                $tmp->comp_ns = $namespace.'\\'.$class_name;
                $relative_path = $v->getRelativePath();
                Assert::string($relative_path = Str::replace('/', '\\', $relative_path), 'wip');

                if ('' !== $relative_path) {
                    $tmp->comp_name = '';
                    $piece = collect(explode('\\', $relative_path))
                        ->map(
                            function ($item) {
                                return Str::slug(Str::snake($item));
                            }
                        )
                        ->implode('.');
                    $tmp->comp_name .= $piece;
                    Assert::string($comp_name = Str::replace('\\', ' ', $class_name), 'wip');
                    $tmp->comp_name .= '.'.Str::slug(Str::snake($comp_name));
                    $tmp->comp_name = $prefix.$tmp->comp_name;
                    $tmp->comp_ns = $namespace.'\\'.$relative_path.'\\'.$class_name;
                    $tmp->class_name = $relative_path.'\\'.$tmp->class_name;
                }

                $comps[] = $tmp;
            }
        }
        $content = json_encode($comps);
        // if (false === $content) {
        //    throw new \Exception('can not decode json');
        // }
        $old_content = '';
        if (File::exists($components_json)) {
            $old_content = File::get($components_json);
        }
        if ($old_content !== $content) {
            //  File::put($components_json, $content);
        }

        return $comps;
    }

    /**
     * Undocumented function.
     */
    public static function getNiceFileSize(int $bytes, bool $binaryPrefix = true): string
    {
        if ($binaryPrefix) {
            $unit = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
            if (0 === $bytes) {
                return '0 '.$unit[0];
            }

            return @round($bytes / 1024 ** ($i = floor(log($bytes, 1024))), 2).' '.(isset($unit[$i]) ? $unit[$i] : 'B');
        }
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        if (0 === $bytes) {
            return '0 '.$unit[0];
        }

        return @round($bytes / 1000 ** ($i = floor(log($bytes, 1000))), 2).' '.(isset($unit[$i]) ? $unit[$i] : 'B');
    }

    /**
     * Undocumented function.
     *
     * @param class-string $class_name
     */
    public static function getFileNameByClassName(string $class_name): ?string
    {
        if (! class_exists($class_name)) {
            return null;
        }
        // try {
        $a = new \ReflectionClass($class_name);
        // 856    Dead catch - Exception is never thrown in the try block.

        // } catch (\Exception $e) {
        //    return null;
        // }
        if (false === $a->getFileName()) {
            return null;
        }

        return $a->getFileName();
    }

    public static function url2Path(string $url): string
    {
        $path = Str::after($url, url('/'));

        return public_path($path);
    }

    public static function vitePath(string $path): string
    {
        $url = Vite::asset($path);

        return self::url2Path($url);
    }

    public static function viteCopy(string $from, string $to): bool
    {
        $url = Vite::asset($from);

        $fromPath = self::url2Path($url);

        self::copy($fromPath, $to);

        return true;
    }
}

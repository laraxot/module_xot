<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Str;

use function is_array;

use Route;

use function Safe\preg_replace;

use Webmozart\Assert\Assert;

/**
 * Class RouteDynService.
 */
class RouteDynService
{
    protected static ?string $namespace_start = '';

    protected static ?string $curr = null;

    public static function getGroupOpts(array $v, ?string $namespace): array
    {
        $group_opts = [
            'prefix' => self::getPrefix($v, $namespace),
            'namespace' => self::getNamespace($v, $namespace),
            'as' => self::getAs($v, $namespace),
        ];

        return $group_opts;
    }

    // ret false|mixed|string|string[]

    public static function getPrefix(array $v, ?string $namespace): string
    {
        if (\in_array('prefix', array_keys($v), true)) {
            return $v['prefix'];
        }
        $prefix = mb_strtolower($v['name']);
        // /*
        $param_name = self::getParamName($v, $namespace);
        if ('' !== $param_name) {
            /*
            Call to function is_array() with string will always evaluate to false.
            if (\is_array($param_name)) {
                return $prefix.'/{'.\implode('}/{', $param_name).'}';
            }
            */
            return $prefix.'/{'.$param_name.'}';
        }
        // */
        /*
        $params_name=self::getParamsName($v,$namespace);
        if($params_name!=[]){
        return $prefix.'/{'.implode('}/{',$params_name).'}';
        }
         */
        return $prefix;
    }

    public static function getAs(array $v, ?string $namespace): string
    {
        if (\in_array('as', array_keys($v), true)) {
            return $v['as'];
        }
        $as = (string) mb_strtolower($v['name']).'';
        $as = str_replace('/', '.', $as);
        Assert::string($as = preg_replace('/{.*}./', '', $as));

        $as = str_replace('{', '', $as);
        $as = str_replace('}', '', $as);
        Assert::string($as);

        return $as.'.';
    }

    public static function getNamespace(array $v, ?string $namespace): ?string
    {
        if (\in_array('namespace', array_keys($v), true)) {
            return $v['namespace'];
        }
        // if($namespace!=null){
        $namespace = $v['name'];
        // }
        $namespace = str_replace('{', '', $namespace);
        $namespace = str_replace('}', '', $namespace);
        if ('' === $namespace) {
            return null;
        }
        if (\is_array($namespace)) {
            throw new \Exception('namespace is array');
        }

        return Str::studly($namespace);
    }

    public static function getAct(array $v, ?string $namespace): string
    {
        if (\in_array('act', array_keys($v), true)) {
            return $v['act'];
        }
        $v['act'] = $v['name'];
        $v['act'] = preg_replace('/{.*}\//', '', $v['act']);
        if (null === $v['act']) {
            $v['act'] = '';
        }
        $v['act'] = str_replace('/', '_', $v['act']);
        if (! \is_string($v['act'])) {
            throw new \Exception('act is not a string');
        }
        $v['act'] = Str::camel($v['act']);
        $v['act'] = str_replace('{', '', $v['act']);
        $v['act'] = str_replace('}', '', $v['act']);
        // camel_case foo_bar  => fooBar
        // studly_case foo_bar => FooBar
        return Str::camel($v['act']);
    }

    public static function getParamName(array $v, ?string $namespace): string
    {
        if (\in_array('param_name', array_keys($v), true)) {
            return $v['param_name'];
        }
        $param_name = 'id_'.$v['name'];
        $param_name = str_replace('{', '', $param_name);
        $param_name = str_replace('}', '', $param_name);
        // $param_name=null;
        $param_name = mb_strtolower($param_name);

        return $param_name;
    }

    /**
     * @return array|false|string|string[]
     */
    public static function getParamsName(array $v, ?string $namespace)
    {
        $param_name = self::getParamName($v, $namespace);
        /*
        Call to function is_array() with string will always evaluate to false.
        if (! \is_array($param_name)) {
            $params_name = [$param_name];
        } else {
            $params_name = $param_name;
        }
        */
        $params_name = [$param_name];

        return $params_name;
    }

    public static function getResourceOpts(array $v, ?string $namespace): array
    {
        $param_name = self::getParamName($v, $namespace);
        $params_name = self::getParamsName($v, $namespace);
        if (! \is_array($params_name)) {
            throw new \Exception('params_name is not an array');
        }
        $opts = [
            'parameters' => [(string) mb_strtolower($v['name']) => implode('}/{', $params_name)],
            'names' => self::prefixedResourceNames(self::getAs($v, $namespace)),
        ];
        if (isset($v['only'])) {
            $opts['only'] = $v['only'];
        }
        if ('' === $param_name && ! isset($opts['only'])) {
            $opts['only'] = ['index'];
        }
        $where = [];
        foreach ($params_name as $pn) {
            $where[$pn] = '[0-9]+';
        }
        $opts['where'] = $where; // se c'e' "id_" di sicuro e' un numero

        return $opts;
    }

    public static function getController(array $v, ?string $namespace): string
    {
        if (\in_array('controller', array_keys($v), true)) {
            return $v['controller'];
        }
        $v['controller'] = $v['name'];
        $v['controller'] = str_replace('/', '_', $v['controller']);
        $v['controller'] = str_replace('{', '', $v['controller']);
        $v['controller'] = str_replace('}', '', $v['controller']);
        if (! \is_string($v['controller'])) {
            throw new \Exception('controller is not a string');
        }

        $v['controller'] = Str::studly($v['controller']);
        // camel_case foo_bar  => fooBar
        // studly_case foo_bar => FooBar
        $v['controller'] = $v['controller'].'Controller';

        return $v['controller'];
    }

    public static function getUri(array $v, ?string $namespace): string
    {
        $uri = mb_strtolower($v['name']);
        /*
        $v['prefix']=self::getPrefix($v,$namespace);
        if(isset($v['prefix'])){ //------------ !!!!! da verificare che non faccia danni
        $uri=$v['prefix'].'/'.$uri;
        }
         */
        return $uri;
    }

    public static function getMethod(array $v, ?string $namespace): array
    {
        if (\in_array('method', array_keys($v), true)) {
            return $v['method'];
        }

        return ['get', 'post'];
    }

    public static function getUses(array $v, ?string $namespace): string
    {
        $controller = self::getController($v, $namespace);
        $act = self::getAct($v, $namespace);
        $uses = $controller.'@'.$act;

        return $uses;
    }

    public static function getCallback(array $v, ?string $namespace, ?string $curr): array
    {
        $as = Str::slug($v['name']); // !!!!!! test da controllare
        $uses = self::getUses($v, $namespace);
        if (null !== $curr) {
            $uses = '\\'.self::$namespace_start.'\\'.$curr.'\\'.$uses;
        } else {
            $uses = '\\'.self::$namespace_start.'\\'.$uses;
        }
        $callback = ['as' => $as, 'uses' => $uses];

        return $callback;
    }

    public static function dynamic_route(array $array, string $namespace = null, string $namespace_start = null, string $curr = null): void
    {
        if (null !== $namespace_start) {
            self::$namespace_start = $namespace_start;
        } /*
        if($curr!=null){
        static::$curr=$curr;
        }*/
        reset($array);
        foreach ($array as $k => $v) {
            $group_opts = self::getGroupOpts($v, $namespace);
            $v['group_opts'] = $group_opts;
            self::createRouteResource($v, $namespace);
            \Route::group(
                $group_opts,
                function () use ($v, $namespace, $curr) {
                    self::createRouteActs($v, $namespace, $curr);
                    self::createRouteSubs($v, $namespace, $curr);
                }
            );
        } // end foreach
    }

    // end function

    // --------------------------------------------------------------------------------

    public static function createRouteResource(array $v, ?string $namespace): void
    {
        if (null === $v['name']) {
            return;
        }
        $opts = self::getResourceOpts($v, $namespace);
        $controller = self::getController($v, $namespace);
        $name = mb_strtolower($v['name']);
        \Route::resource($name, $controller, $opts);
        // ->where(['container1' => "^((?!create|edit).)*$"])  //BadMethodCallException Method Illuminate\Routing\PendingResourceRegistration::where does not exist.
        //  ->middleware('manageContainer','container1')// ->where(['id_'.$v['name'] => '[0-9]+']);
    }

    // ------------------------------------------------------------------------------

    public static function createRouteSubs(array $v, ?string $namespace, ?string $curr): void
    {
        if (! isset($v['subs'])) {
            return;
        }
        $sub_namespace = self::getNamespace($v, $namespace);
        /*
        if(self::$curr==null){
        self::$curr=$sub_namespace;
        }else{
        if(self::$curr!=$sub_namespace){
        self::$curr=self::$curr.'\\'.$sub_namespace;
        }
        }
         */
        if (null === $curr) {
            $curr = $sub_namespace;
        } else {
            $piece = explode('\\', $curr);
            if (last($piece) !== $sub_namespace && $curr !== $sub_namespace) {
                $curr .= '\\'.$sub_namespace;
            }
        }

        self::dynamic_route($v['subs'], $sub_namespace, null, $curr);
    }

    // ---------------------------------------------------

    public static function createRouteActs(array $v, ?string $namespace, ?string $curr): void
    {
        if (! isset($v['acts'])) {
            return;
        }
        reset($v['acts']);

        $controller = self::getController($v, $namespace);
        foreach ($v['acts'] as $k1 => $v1) {
            // try {
            $v1['controller'] = $controller; // le acts hanno il controller del padre
            // } catch (\Exception $e) {
            //    dddx([
            //        'message' => $e->getMessage(),
            //        'k1' => $k1,
            //        'v1' => $v1,
            //        'controller' => $controller,
            //    ]);
            // }
            $method = self::getMethod($v1, $namespace);
            $uri = self::getUri($v1, $namespace);
            $callback = self::getCallback($v1, $namespace, $curr);
            /*
            Else branch is unreachable because previous condition is always true.
            if (\is_array($method)) {
                Route::match($method, $uri, $callback);
            } else {
                Route::$method($uri, $callback);
            }
            */
            \Route::match($method, $uri, $callback);
        } // endforeach
    }

    // /--------------------------------------------------------
    /* ?? deprecated ??
    public static function routes() {
        if ('' != \Request::path()) {
            $tmp = \explode('/', \Request::path());
            $tmp = \array_slice($tmp, 0, 2);
            $tmp = \implode('_', $tmp);
            //echo '<h3>tmp = '.$tmp.'</h3>';die();
            $filename = 'web_'.$tmp.'.php';

            $tmp = \debug_backtrace();
            dd($tmp[3]['class']);

            $filename_dir = __DIR__.\DIRECTORY_SEPARATOR.$filename;
            echo '<h3>tmp = '.$filename_dir.'</h3>';
            die();
            if (\file_exists($filename_dir)) {
                require $filename_dir;
            }
        }
    }
    */
    // end routes
    // ------------------------------------------------------------------

    public static function prefixedResourceNames(string $prefix): array
    {
        if ('.' === mb_substr($prefix, -1)) {
            $prefix = mb_substr($prefix, 0, -1);
        }
        // Strict comparison using === between null and non-empty-string will always evaluate to false.
        // if ('' === $prefix || null === $prefix) {
        if ('' === $prefix) {
            return ['index' => $prefix.'index', 'create' => $prefix.'create', 'store' => $prefix.'store', 'show' => $prefix.'show', 'edit' => $prefix.'edit', 'update' => $prefix.'update', 'destroy' => $prefix.'destroy'];
        }
        $prefix = mb_strtolower($prefix);

        return ['index' => $prefix.'.index', 'create' => $prefix.'.create', 'store' => $prefix.'.store', 'show' => $prefix.'.show', 'edit' => $prefix.'.edit', 'update' => $prefix.'.update', 'destroy' => $prefix.'.destroy'];
    }

    // end prefixedResourceNames

    // --------------------------------------------------

    public static function getContainerActs(): array
    {
        $cont_acts = [
            [
                'name' => 'Edit',
                'act' => 'indexEdit',
            ], // end act_n
            [
                'name' => 'Order',
                'act' => 'indexOrder',
            ], // end act_n
            [
                'name' => 'Attach',
                'act' => 'indexAttach',
            ], // end act_n
        ];

        return $cont_acts;
    }

    public static function getItemActs(): array
    {
        $acts = [
            // ['name' => 'attach'], //end act_n
            ['name' => 'detach', 'method' => ['DELETE', 'GET']], // end act_n
            // ['name' => 'moveUp', 'method' => ['PUT', 'GET']],   // se uso "order" questi non mi servono
            // ['name' => 'moveDown', 'method' => ['PUT', 'GET']],
        ]; // end acts

        return $acts;
    }

    public static function generate(int $n = 0): array
    {
        if ($n > 4) {
            return [];
        }

        return [
            [
                'name' => '{container'.$n.'}',
                'param_name' => '',
                'as' => 'container'.$n.'.index_',
                'acts' => self::getContainerActs(),
                // 'only'=>[],
            ],
            [
                'name' => '{container'.$n.'}',
                'param_name' => 'item'.$n.'',
                'acts' => self::getItemActs(),
                'subs' => self::generate($n + 1),
            ],
        ];
    }

    // --------------------------------------------------
}

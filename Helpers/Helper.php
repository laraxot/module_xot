<?php

use Illuminate\Support\Facades\File;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\TenantService as Tenant;

//------------------------------------------------
if (! \function_exists('snake_case')) {
    function snake_case($str) {
        return Str::snake($str);
    }
}

if (! \function_exists('str_slug')) {
    function str_slug($str) {
        return Str::slug($str);
    }
}

if (! function_exists('str_singular')) {
    function str_singular($str) {
        return Str::singular($str);
    }
}

if (! function_exists('starts_with')) {
    function starts_with($str, $str1) {
        return Str::startsWith($str, $str1);
    }
}

if (! function_exists('ends_with')) {
    function ends_with($str, $str1) {
        return Str::endsWith($str, $str1);
    }
}

if (! function_exists('str_contains')) {
    function str_contains($str, $str1) {
        return Str::contains($str, $str1);
    }
}

//-------------------------------------------

if (! \function_exists('dddx')) {
    function dddx($params) {
        $tmp = \debug_backtrace();
        $file = $tmp[0]['file'];
        $file = \str_replace('/', DIRECTORY_SEPARATOR, $file);
        $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $doc_root = \str_replace('/', DIRECTORY_SEPARATOR, $doc_root);
        $dir_piece = \explode(DIRECTORY_SEPARATOR, __DIR__);
        $dir_piece = \array_slice($dir_piece, 0, -6);
        $dir_copy = \implode(DIRECTORY_SEPARATOR, $dir_piece);
        $file = \str_replace($dir_copy, $doc_root, $file);
        dd(
            [
                '_' => $params,
                'line' => $tmp[0]['line'],
                'file' => $tmp[0]['file'],
                //'file_1' => $file, //da sistemare
            ]
        );
    }
}

if (! \function_exists('getFilename')) {
    function getFilename($params) {
        $tmp = \debug_backtrace();
        $class = class_basename($tmp[1]['class']);
        $func = $tmp[1]['function'];
        $params_list = collect($params)->except(['_token', '_method'])->implode('_');
        $filename = Str::slug(
            \str_replace('Controller', '', $class).
                    '_'.\str_replace('do_', '', $func).
                    '_'.$params_list
        );

        return $filename;
    }
}
if (! \function_exists('req_uri')) {
    function req_uri() {
        $req_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        return $req_uri;
    }
}

if (! \function_exists('in_admin')) {
    function in_admin($params = []) {
        //return 'admin' == \Request::segment(1);
        return inAdmin($params);
    }
}
if (! \function_exists('inAdmin')) {
    function inAdmin($params = []) {
        if (isset($params['in_admin'])) {
            return $params['in_admin'];
        }
        //dddx(ThemeService::__getStatic('in_admin'));
        if (null !== config()->get('in_admin')) {
            return config()->get('in_admin');
        }
        if ('admin' == \Request::segment(1)) {
            return true;
        }
        $segments = (\Request::segments());
        if (count($segments) > 0 && 'livewire' == $segments[0]) {
            if (true == session()->get('in_admin')) {
                return true;
            }
        }

        return false;
        //dddx(session('in_admin'));
        /*
        $segments = request()->segments();
        dddx($_SERVER);

        return 'admin' == 'aa';
        */
        //return in_admin();
    }
}

/*
     * Return true if current page is home.
     *
     * @return bool
     */
    if (! \function_exists('isHome')) {
        function isHome() {
            return Route::is('home');
        }
    }
    /*
     * Return true if current page is an admin home page.
     *
     * @return bool
     */
    if (! \function_exists('isAdminHome')) {
        function isAdminHome() {
            return URL::current() == route('admin.index');
        }
    }

    /*
     * https://gist.github.com/atorscho/5bcf63d077c11ed0e8ce
     * Return true if current page is an admin page.
     *
     * @return bool
     */
    if (! \function_exists('isAdmin')) {
        function isAdmin() {
            return Route::is('*admin*');
        }
    }

    /*
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
if (! \function_exists('fullTextWildcards')) {
    /*protected */ function fullTextWildcards($term) {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if (strlen($word) >= 3) {
                $words[$key] = '+'.$word.'*';
            }
        }

        $searchTerm = implode(' ', $words);

        return $searchTerm;
    }
}

if (! \function_exists('params2ContainerItem')) {
    function params2ContainerItem($params = null) {
        if (null == $params) {
            $params = \Route::current()->parameters();
        }
        $container = [];
        $item = [];
        foreach ($params as $k => $v) {
            $pattern = '/(container|item)([0-9]+)/';
            preg_match($pattern, $k, $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                $sk = $matches[1];
                $sv = $matches[2];
                $$sk[$sv] = $v;
            }
        }

        return [$container, $item];
    }
}

if (! \function_exists('getModelFields')) {
    function getModelFields($model) {
        $fields = $model->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($model->getTable());

        return $fields;
    }
}

if (! \function_exists('getModuleFromModel')) {
    function getModuleFromModel($model) {
        $class = get_class($model);
        $module_name = Str::before(Str::after($class, 'Modules\\'), '\\Models\\');
        $mod = \Module::find($module_name);

        return $mod;
    }
}

if (! \function_exists('getModuleNameFromModel')) {
    function getModuleNameFromModel($model) {
        if (! is_object($model)) {
            return null;
        }
        $class = get_class($model);
        $module_name = Str::before(Str::after($class, 'Modules\\'), '\\Models\\');

        return $module_name;
    }
}

if (! \function_exists('getModuleNameFromModelName')) {
    function getModuleNameFromModelName($model_name) {
        $model = Tenant::model($model_name);

        return getModuleNameFromModel($model);
    }
}

if (! \function_exists('getTransformerFromModel')) {
    function getTransformerFromModel($model, $type = 'Resource') {
        $class = get_class($model);
        $module_name = getModuleNameFromModel($model);
        $transformer = '\\Modules\\'.$module_name.'\Transformers\\'.class_basename($model).''.$type;
        //ddd($transformer.' '.class_exists($transformer));
        if (! class_exists($transformer)) {
            ddd('preso');
        }

        return $transformer;
    }
}

if (! \function_exists('getAllModulesModels')) {
    function getAllModulesModels() {
        $res = [];
        $modules = Module::all();
        foreach ($modules as $module) {
            $tmp = getModuleModels($module->getName());
            $res = array_merge($res, $tmp);
        }

        return $res;
    }
}

if (! \function_exists('getModuleModels')) {
    function getModuleModels($module) {
        if (Str::startsWith($module, 'trasferte')) { //caso eccezzionale
            $module = 'trasferte';
        }
        if (null == $module) {
            return [];
        }
        $mod = \Module::find($module);
        if (null == $mod) {
            return [];
        }
        $mod_path = $mod->getPath().'/Models';
        $mod_path = str_replace(['\\', '/'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $mod_path);
        $files = File::files($mod_path);
        $data = [];
        $ns = 'Modules\\'.$mod->getName().'\\Models';  // con la barra davanti non va il search ?
        foreach ($files as $file) {
            $filename = $file->getRelativePathname();
            $ext = '.php';
            if (Str::endsWith($filename, $ext)) {
                $tmp = new \stdClass();
                $name = substr(($filename), 0, -strlen($ext));
                $tmp->class = $ns.'\\'.$name;
                $name = Str::snake($name);
                $tmp->name = $name;
                $reflection_class = new ReflectionClass($tmp->class);
                if (! $reflection_class->isAbstract()) {
                    $data[$tmp->name] = $tmp->class;
                }
            }
        }

        return $data;
    }
}

if (! \function_exists('xotModel')) {
    function xotModel($name) {
        return Tenant::model($name);
    }
}

if (! \function_exists('transFields')) {
    function transFields($params) {
        $model = Form::getModel();
        $module_name = getModuleNameFromModel($model);
        $ns = Str::lower($module_name);
        $trans_root = $ns.'::'.Str::snake(class_basename($model));
        //ddd() );
        //debug_getter_obj(['obj'=>$module]);
        //ddd($module_name->getNamespace());
        $view = 'unknown';
        extract($params);
        //ddd($params);
        if (isset($attributes)) {
            extract($attributes);
        }
        if (isset($options['field'])) {
            $field = $options['field'];
            $ris = $field;
        } else {
            $ris = new \stdClass();
        }

        $start = 0;
        if (in_admin()) {
            $start = 1;
        }
        if (! isset($ris->name)) {
            $ris->name = $name;
        }

        $ris->name_dot = bracketsToDotted($name);

        $pattern = '/\.[0-9]+\./m';
        $ris->name_dot = preg_replace($pattern, '.', $ris->name_dot);

        list($ns, $key) = explode('::', $view);
        if (null == $module_name) {
            $trans_root = $ns.'::'.implode('.', array_slice(explode('.', $key), $start, -1));
        }

        $trans_fields = ['label', 'placeholder', 'help'];
        foreach ($trans_fields as $tf) {
            $trans = $trans_root.'.field.'.Str::snake($ris->name_dot).'_'.$tf;
            $ris->$tf = isset($$tf) ? $$tf : trans($trans);
            if ($ris->$tf == $trans && ! config('xra.show_trans_key')) {
                $ris->$tf = $ris->name_dot;
                //$ris->$tf = $trans;
            }
        }
        if ($ris->help == $ris->name_dot) {
            $ris->help = '';
        }

        $attributes = $params;
        $attrs_default = ['class' => 'form-control', 'placeholder' => $ris->placeholder];
        if (! isset($params['attributes'])) {
            $params['attributes'] = [];
        }

        $ris->attributes = collect(array_merge($attrs_default, $attributes, $params['attributes']))
                        ->filter(function ($item, $key) {
                            return in_array($key, ['style', 'class', 'placeholder', 'readonly', 'id', 'value', 'name']) || Str::startsWith($key, 'data-');
                        })
                        //->only('class','placeholder','readonly')
                        ->all();
        $ris->params = $params;

        if (! isset($ris->col_bs_size)) {
            $ris->col_bs_size = 12;
        }

        return $ris;
    }
}

if (! \function_exists('deltaTime')) {
    function deltaTime() {
        echo '<h3>Time :'.class_basename($this).' '.(microtime(true) - LARAVEL_START).'</h3>';
    }
}

if (! \function_exists('debug_getter_obj')) {
    function debug_getter_objOLD($params) {
        extract($params);
        $methods = collect(get_class_methods($obj))->filter(function ($item) {
            $exclude = [
                //--Too few arguments to function
                'getRelationExistenceQuery',
                'getRelationExistenceQueryForSelfRelation',
                'getRelationExistenceCountQuery',
                'getMorphedModel',
                'getRelationExistenceQueryForSelfJoin',
                'getPlatformOption',
                'getCustomSchemaOption',
                'getShortestName',
                'getFullQualifiedName',
                'getQuotedName',
                //---
                'getAttribute',
                'getAttributeValue',
                'getRelationValue',
                'getGlobalScope',
                'getActualClassNameForMorph',
                'getRelation',
                //---------
                'getDataStartAttribute',
                'getDataAttribute',
                'getMacro',
                //--altri errori --
            ];

            return Str::startsWith($item, 'get') && ! in_array($item, $exclude);
        })->map(function ($item) use ($obj) {
            $tmp = [];
            $tmp['name'] = $item;
            try {
                $tmp['ris'] = $obj->$item();
            } catch (\Exception $e) {
                $tmp['ris'] = $e->getMessage();
            }

            return $tmp;
        });
        //->dd();
        $html = '<table border="1">
        <thead>
        <tr>
        <th>Name</th>
        <th>Ris</th>
        </tr>
        </thead>';
        foreach ($methods as $k => $v) {
            $html .= '<tr>';
            $html .= '<td>'.$v['name'].'</td>';
            $val = $v['ris'];
            if (is_object($val)) {
                $val = '(Object) '.get_class($val);
            }
            if (is_array($val)) {
                $val = var_export($val, true);
            }
            $html .= '<td>'.$val.'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        echo $html;
        ddd($methods);
    }//end function

    function debug_getter_obj($params) {
        extract($params);
        $methods = get_class_methods($obj);
        $data = [];
        if (! is_array($methods)) {
            return $data;
        }
        $methods = collect($methods)->filter(function ($item) {
            $exclude = [
                'forceDelete',
                'forceCreate',
            ];
            if (! Str::startsWith($item, '__') && ! in_array($item, $exclude)) {
                return true;
            }

            return false;
        })->all();
        //ddd($methods);
        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod($obj, $method);
            $args = $reflection->getParameters();
            if (0 == count($args) && $reflection->class == get_class($obj)) {
                try {
                    $return = $reflection->invoke($obj);
                    //$check = ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation);
                    //if ($check) {
                    //$related_model = (new \ReflectionClass($return->getRelated()))->getName();
                    $msg = [
                        'name' => $reflection->name,
                        'type' => class_basename($return),
                        'ris' => $return,
                        //'check'=>$check,
                        //$msg['type']=(new \ReflectionClass($return))->getShortName();
                        //'model' => $related_model,
                    ];
                    $data[] = $msg;
                    //}
                } catch (ErrorException $e) {
                }
            }
        }
        ddd($data);

        return $data;
    }
}//end exists

if (! \function_exists('bracketsToDotted')) {
    // privacies[111][pivot][title] => privacies.111.pivot.title
    function bracketsToDotted($str, $quotation_marks = '') {
        return str_replace(['[', ']'], ['.', ''], $str);
    }
}
if (! \function_exists('dottedToBrackets')) {
    // privacies.111.pivot.title => privacies[111][pivot][title]
    function dottedToBrackets($str, $quotation_marks = '') {
        $str = collect(explode('.', $str))->map(function ($v, $k) {
            return 0 == $k ? $v : '['.$v.']';
        })->implode('');

        return $str;
    }
}

if (! \function_exists('array_merge_recursive_distinct')) {
    function array_merge_recursive_distinct(array &$array1, array &$array2) {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}

/*
|--------------------------------------------------------------------------
| Laravel 5 - URL Query String Helper
|--------------------------------------------------------------------------
|
| A helper function to take a URL string then quickly and easily add query
| string parameters to it, or change existing ones.
|
| url_queries(['order' => 'desc', 'page' => 2],
|             'https://app.dev/users/?sort=username&order=asc');
| // https://app.dev/users/?sort=username&order=desc&page=2
|
https://gist.github.com/ImLiam/49c420ddb2db881afd59d77635d039f8
*/
    if (! function_exists('url_queries')) {
        /**
         * Modifies the query strings in a given (or the current) URL.
         *
         * @param array       $queries Indexed array of query parameters
         * @param string|null $url     URL to use parse. If none is supplied, the current URL of the page load will be used
         *
         * @return string The updated query string
         */
        function url_queries(array $queries, string $url = null) {
            // If a URL isn't supplied, use the current one
            if (! $url) {
                $url = \Request::fullUrl();
            }
            // Split the URL down into an array with all the parts separated out
            $url_parsed = parse_url($url);
            // Turn the query string into an array
            $url_params = [];
            if (isset($url_parsed) && isset($url_parsed['query'])) {
                parse_str($url_parsed['query'], $url_params);
            }
            // Merge the existing URL's query parameters with our new ones
            $url_params = array_merge($url_params, $queries);
            // Build a new query string from our updated array
            $string_query = http_build_query($url_params);
            // Add the new query string back into our URL
            $url_parsed['query'] = $string_query;
            // Build the array back into a complete URL string
            $url = build_url($url_parsed);

            return $url;
        }
    }
    if (! function_exists('build_url')) {
        /**
         * Rebuilds the URL parameters into a string from the native parse_url() function.
         *
         * @param array $parts The parts of a URL
         *
         * @return string The constructed URL
         */
        function build_url(array $parts) {
            return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '').
                ((isset($parts['user']) || isset($parts['host'])) ? '//' : '').
                (isset($parts['user']) ? "{$parts['user']}" : '').
                (isset($parts['pass']) ? ":{$parts['pass']}" : '').
                (isset($parts['user']) ? '@' : '').
                (isset($parts['host']) ? "{$parts['host']}" : '').
                (isset($parts['port']) ? ":{$parts['port']}" : '').
                (isset($parts['path']) ? "{$parts['path']}" : '').
                (isset($parts['query']) ? "?{$parts['query']}" : '').
                (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
        }
    }

if (! function_exists('getRelationships')) {
    function getRelationships($model) { //working
        $methods = get_class_methods($model);
        $data = [];
        if (! is_array($methods)) {
            return $data;
        }
        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod($model, $method);
            $args = $reflection->getParameters();
            if (0 == count($args) && $reflection->class == get_class($model)) {
                try {
                    $return = $reflection->invoke($model);
                    $check = ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation);
                    if ($check) {
                        $related_model = (new \ReflectionClass($return->getRelated()))->getName();
                        $msg = [
                            'name' => $reflection->name,
                            'type' => class_basename($return),
                            //'check'=>$check,
                            //$msg['type']=(new \ReflectionClass($return))->getShortName();
                            'model' => $related_model,
                        ];
                        $data[] = $msg;
                    }
                } catch (ErrorException $e) {
                }
            }
        }

        return $data;
    }
}

    /*
    public function getRelationshipsV2($model){
        $relationships = [];

        foreach((new \ReflectionClass($model))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
            if ($method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                if ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $relationships[$method->getName()] = [
                        'name' => $method->getName(),
                        'type' => (new \ReflectionClass($return))->getShortName(),
                        'model' => (new \ReflectionClass($return->getRelated()))->getName()
                    ];
                }
            } catch(ErrorException $e) {}
        }

        return $relationships;
    }
    */

/*
 * https://chasingcode.dev/blog/laravel-global-url-helpers/
 * URL before:
 * https://example.com/orders/123?order=ABC009&status=shipped
 *
 * 1. removeQueryParams(['status'])
 * 2. removeQueryParams(['status', 'order'])
 *
 * URL after:
 * 1. https://example.com/orders/123?order=ABC009
 * 2. https://example.com/orders/123
 */

if (! function_exists('removeQueryParams')) {
    function removeQueryParams(array $params = []) {
        $url = url()->current(); // get the base URL - everything to the left of the "?"
        $query = request()->query(); // get the query parameters (what follows the "?")

        foreach ($params as $param) {
            unset($query[$param]); // loop through the array of parameters we wish to remove and unset the parameter from the query array
        }

        return $query ? $url.'?'.http_build_query($query) : $url; // rebuild the URL with the remaining parameters, don't append the "?" if there aren't any query parameters left
    }
}

/*
 * https://chasingcode.dev/blog/laravel-global-url-helpers/
 * URL before:
 * https://example.com/orders/123?order=ABC009
 *
 * 1. addQueryParams(['status' => 'shipped'])
 * 2. addQueryParams(['status' => 'shipped', 'coupon' => 'CCC2019'])
 *
 * URL after:
 * 1. https://example.com/orders/123?order=ABC009&status=shipped
 * 2. https://example.com/orders/123?order=ABC009&status=shipped&coupon=CCC2019
 */
if (! function_exists('addQueryParams')) {
    function addQueryParams(array $params = []) {
        $query = array_merge(
            request()->query(),
            $params
        ); // merge the existing query parameters with the ones we want to add

        return url()->current().'?'.http_build_query($query); // rebuild the URL with the new parameters array
    }
}

if (! function_exists('isJson')) {
    /*
    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    */
    /*
    function isJson($string) {
    return ((is_string($string) &&
            (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
    }
*/
    function isJson($string) {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }
}

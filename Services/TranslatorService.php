<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
//---- services ---
use Illuminate\Translation\Translator as BaseTranslator;
use Modules\Theme\Services\ThemeService;

//ddd('leggo');

/**
 * Class TranslatorService.
 */
class TranslatorService extends BaseTranslator {
    /**
     * get.
     *
     * @param string      $key
     * @param string|null $locale
     * @param bool        $fallback
     *
     * @return array|string
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true) {
        $translation = parent::get($key, $replace, $locale, $fallback);
        //echo '<br>['.$key.']['.$translation.']';
        //$langs=ThemeService::__merge('langs', [$key=>$translation]);
        //$cache_key=Str::slug(req_uri().'_langs');
        //Cache::put($cache_key,$langs);
        //echo '<pre>';print_r($langs);echo '</pre>';
        /*
        if ($translation === $key) {
            Log::warning('Language item could not be found.', [
                'language' => $locale ?? config('app.locale'),
                'id' => $key,
                'url' => config('app.url')
            ]);
        }
        */

        return $translation;
    }

    /**
     * getFromJson.
     *
     * @param mixed       $key
     * @param string|null $locale
     *
     * @return array|string
     */
    public function getFromJson($key, array $replace = [], $locale = null) {
        return $this->get($key, $replace, $locale);
    }

    public static function parse(array $params): array {
        $lang = app()->getLocale();
        extract($params);
        if (! isset($key)) {
            dddx(['err' => 'key is missing']);

            return [];
        }
        $translator = app('translator');
        $tmp = ($translator->parseKey($key));
        $namespace = $tmp[0];
        $group = $tmp[1];
        $item = $tmp[2];
        $trans = trans();
        $path = collect($trans->getLoader()->namespaces())->flip()->search($namespace);
        $filename = $path.'/'.$lang.'/'.$group.'.php';
        $filename = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $filename);
        $lang_dir = dirname(dirname($filename));

        return [
            'key' => str_replace(['[', ']'], ['.', ''], $key),
            'namespace' => $namespace,
            'group' => $group,
            'ns_group' => $namespace.'::'.$group,
            'item' => $item,
            'filename' => $filename,
            'file_exists' => File::exists($filename),
            'lang_dir' => $lang_dir,
            'dir_exists' => File::exists($lang_dir), //dir without lang
        ];
    }

    /**
     * @return void
     */
    public static function store(array $data) {
        $data = collect($data)->map(
            function ($v, $k) {
                $item = self::parse(['key' => $k]);
                $item['value'] = $v;

                return $item;
            }
        )
        //->dd()
        ->filter(function ($v, $k) {
            return $v['dir_exists'] && strlen($v['lang_dir']) > 3;
        })
        ->groupBy(['ns_group'])  //risparmio salvataggi
        ->all();
        //ddd($data);
        foreach ($data as $ns_group => $data0) {
            $rows = trans($ns_group);

            if (! is_array($rows)) {
                //ddd($rows);  //---- dovrei leggere il file o controllarlo intanto lo blokko non voglio sovrascrivere
                $rows = [];
            }

            foreach ($data0 as $k => $v) {
                $key = Str::after($v['key'], $ns_group.'.');
                Arr::set($rows, $key, $v['value']);
            }

            $data = $rows;
            if (! isset($v)) {
                dddx(['err' => 'v is missing']);

                return;
            }
            $filename = $v['filename'];
            //echo '<h3>['.$filename.']</h3>';
            ArrayService::save(['filename' => $filename, 'data' => $data]);
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public static function set($key, $value) {
        $lang = app()->getLocale();
        if (trans($key) == $value) {
            return;
        } //non serve salvare

        $translator = app('translator');
        $tmp = ($translator->parseKey($key));
        $namespace = $tmp[0];
        $group = $tmp[1];
        $item = $tmp[2];
        $trans = trans();
        $path = collect($trans->getLoader()->namespaces())->flip()->search($namespace);
        $filename = $path.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$group.'.php';

        $trad = $namespace.'::'.$group;
        $rows = (trans($trad));
        $item_keys = explode('.', $item);
        $item_keys = implode('"]["', $item_keys);
        $item_keys = '["'.$item_keys.'"]';
        $str = '$rows'.$item_keys.'="'.$value.'";';
        try {
            eval($str); //fa schifo ma funziona
        } catch (\Exception $e) {
        }
        ArrayService::save(['data' => $rows, 'filename' => $filename]);

        Session::flash('status', 'Modifica Eseguita! ['.$filename.']');

        /*

        ddd($rows)



        ddd($item_keys);

    	ddd($filename);
    	*/
    }
}

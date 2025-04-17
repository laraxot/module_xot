<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use function get_class;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Modules\Xot\Datas\XotData;

/**
 * Class PolicyService.
 */
class PolicyService
{
    // protected static $obj;
    protected static array $in_vars = [];

    protected static array $out_vars = [];
    private static ?PolicyService $instance = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        /*
        if (null == self::$instance) {
            throw new \Exception('something gone bad');
        }
        */
        return self::$instance;
    }

    /**
     * @throws \ReflectionException
     */
    // ret PolicyService|null
    public static function get(object $obj): self
    {
        // self::$obj = $obj;
        $class = \get_class($obj);
        $class_name = class_basename($obj);
        $class_ns = substr($class, 0, -(\strlen($class_name) + 1));

        self::$in_vars['class_name'] = $class_name;
        self::$in_vars['class_type'] = '';
        // if ($obj instanceof \Modules\Cms\Models\Panels\XotBasePanel) {
        // 46     If condition is always false.
        // if (get_class($obj)=='Modules\Cms\Models\Panels\XotBasePanel') {
        //    self::$in_vars['class_type'] = 'panel';
        // }

        self::$in_vars['namespace'] = $class_ns;
        self::$in_vars['class'] = $class;
        $autoloader_reflector = new \ReflectionClass(self::$in_vars['class']);
        $filename = $autoloader_reflector->getFileName();
        if (false === $filename) {
            throw new \Exception('autoloader_reflector error');
        }
        $filename = str_replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $filename);
        self::$in_vars['filename'] = $filename;
        self::$in_vars['dirname'] = \dirname(self::$in_vars['filename']);

        self::$out_vars['class_name'] = $class_name.'Policy';
        self::$out_vars['namespace'] = $class_ns.'\Policies';
        self::$out_vars['class'] = self::$out_vars['namespace'].'\\'.self::$out_vars['class_name'];
        $filename = self::$in_vars['dirname'].'/Policies/'.$class_name.'Policy.php';
        $filename = str_replace(['/', '\\'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $filename);
        self::$out_vars['filename'] = $filename;
        self::$out_vars['dirname'] = \dirname(self::$out_vars['filename']);

        return self::getInstance();
    }

    public static function replaces(array $params = []): array
    {
        $xotData = XotData::make();
        extract(self::$out_vars);
        if (! isset($namespace)) {
            throw new \Exception('namespace is missing');
        }
        if (! isset($class_name)) {
            throw new \Exception('class_name is missing');
        }
        if (! isset($class)) {
            throw new \Exception('class is missing');
        }
        // $user_class = get_class(Auth::user());
        $user_class = $xotData->getUserClass();
        $replaces = [
            'DummyNamespace' => $namespace,
            'DummyClass' => $class_name,
            'DummyFullModel' => $class,
            // 'dummy_id' => $dummy_id,
            'dummy_title' => 'title', // prendo il primo campo stringa
            'NamespacedDummyUserModel' => $user_class,
            'NamespacedDummyModel' => $class,
        ];

        return $replaces;
    }

    public function getClass(): string
    {
        return self::$out_vars['class'];
    }

    public function exists(): bool
    {
        return File::exists(self::$out_vars['filename']);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createIfNotExists(): self
    {
        if ($this->exists()) {
            return self::getInstance(); // se esiste esce;
        }
        $stub_name = 'policy';
        if ('' !== self::$in_vars['class_type']) {
            $stub_name .= '/'.self::$in_vars['class_type'];
        }
        $stub_file = __DIR__.'/../Console/stubs/'.$stub_name.'.stub';

        $stub = File::get($stub_file);

        $replace = self::replaces();
        $stub = str_replace(array_keys($replace), array_values($replace), $stub);

        File::makeDirectory(self::$out_vars['dirname'], $mode = 0777, true, true);

        if (! File::exists(self::$out_vars['filename'])) {
            File::put(self::$out_vars['filename'], $stub);
        } else {
            echo '<h3>['.self::$out_vars['filename'].'] Just exists</h3>';
            dddx(debug_backtrace());
        }

        $res = self::getInstance();

        return $res;
    }
}

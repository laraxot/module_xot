<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;

class PolicyService {
    private static $instance = null;
    //protected static $obj;
    protected static $in_vars = [];
    protected static $out_vars = [];

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get($obj) {
        //self::$obj = $obj;
        $class = get_class($obj);
        $class_name = class_basename($obj);
        $class_ns = substr($class, 0, -(strlen($class_name) + 1));

        self::$in_vars['class_name'] = $class_name; // "LettFPanel"
        self::$in_vars['class_type'] = '';
        if ($obj instanceof  \Modules\Xot\Models\Panels\XotBasePanel) {
            self::$in_vars['class_type'] = 'panel';
        }

        self::$in_vars['namespace'] = $class_ns;
        self::$in_vars['class'] = $class;
        $autoloader_reflector = new \ReflectionClass(self::$in_vars['class']);
        $filename = $autoloader_reflector->getFileName();
        $filename = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $filename);
        self::$in_vars['filename'] = $filename;
        self::$in_vars['dirname'] = dirname(self::$in_vars['filename']);

        self::$out_vars['class_name'] = $class_name.'Policy'; // "LettFPanel"
        self::$out_vars['namespace'] = $class_ns.'\Policies';
        self::$out_vars['class'] = self::$out_vars['namespace'].'\\'.self::$out_vars['class_name'];
        $filename = self::$in_vars['dirname'].'/Policies/'.$class_name.'Policy.php';
        $filename = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $filename);
        self::$out_vars['filename'] = $filename;
        self::$out_vars['dirname'] = dirname(self::$out_vars['filename']);

        return self::getInstance();
    }

    public function getClass() {
        return self::$out_vars['class'];
    }

    public function exists() {
        return File::exists(self::$out_vars['filename']);
    }

    public static function replaces($params = []) {
        extract(self::$out_vars);
        $replaces = [
            'DummyNamespace' => $namespace,
            'DummyClass' => $class_name,
            'DummyFullModel' => $class,
            //'dummy_id' => $dummy_id,
            'dummy_title' => 'title', // prendo il primo campo stringa
            'NamespacedDummyUserModel' => 'Modules\LU\Models\User',
            'NamespacedDummyModel' => $class,
        ];

        return $replaces;
    }

    public function createIfNotExists() {
        if ($this->exists()) {
            return self::getInstance(); //se esiste esce;
        }
        $stub_name = 'policy';
        if ('' != self::$in_vars['class_type']) {
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

        return self::getInstance();
    }
}

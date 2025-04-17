<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use ReflectionClass;

// ----------- Requests ----------

/**
 * Class ModuleService.
 */
class ModuleService
{
    private static ?self $_instance = null;

    public string $name;

    /**
     * getInstance.
     *
     * this method will return instance of the class
     */
    public static function getInstance(): self
    {
        if (! self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Undocumented function.
     */
    public static function make(): self
    {
        return static::getInstance();
    }

    /**
     * Undocumented function.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Undocumented function.
     */
    public function getModels(): array
    {
        /*
        if (null == $module) {
            return [];
        }
        */
        $mod = Module::find($this->name);
        if (null === $mod) {
            return [];
        }
        $mod_path = $mod->getPath().'/Models';
        $mod_path = str_replace(['\\', '/'], [\DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR], $mod_path);
        $files = File::files($mod_path);
        $data = [];
        $ns = 'Modules\\'.$mod->getName().'\\Models';  // con la barra davanti non va il search ?
        foreach ($files as $file) {
            $filename = $file->getRelativePathname();
            $ext = '.php';
            // dddx(['ext' => $file->getExtension(), get_class_methods($file)]);
            if (Str::endsWith($filename, $ext)) {
                $tmp = new \stdClass();

                $name = substr($filename, 0, -\strlen($ext));

                // dddx(['name' => $name, 'name1' => $file->getFilenameWithoutExtension()]);
                /**
                 * @var class-string
                 */
                $class = $ns.'\\'.$name;
                $tmp->class = $class;

                $name = Str::snake($name);
                $tmp->name = $name;
                // 434    Parameter #1 $argument of class ReflectionClass constructor expects class-string<T of object>|T of object, string given.
                try {
                    $reflection_class = new \ReflectionClass($tmp->class);
                    if (! $reflection_class->isAbstract()) {
                        $data[$tmp->name] = $tmp->class;
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $data;
    }
}

<?php
/**
 * @see https://medium.com/@parvej.code/how-to-create-simple-memoization-helper-in-php-1eb3cdbfde7c
 */

declare(strict_types=1);

namespace Modules\Xot\Services;

class Memoization
{
    private static ?self $_instance = null;

    protected array $memoized = [];

    /**
     * Undocumented function.
     */
    public function __construct()
    {
    }

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
     *
     * @return mixed|void
     */
    public function memoize(string $key, \Closure $callback)
    {
        if (! isset($this->memoized[$key])) {
            return $this->memoized[$key] = $callback();
        }

        return $this->memoized[$key];
    }
}

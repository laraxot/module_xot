<?php

declare(strict_types=1);

namespace Modules\Xot\Traits;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait Cacheable.
 */
trait Cacheable
{
    /**
     * Cache instance.
     *
     * @var CacheManager
     */
    protected static ?CacheManager $cache = null;

    /**
     * Flush the cache after create/update/delete events.
     */
    protected bool $eventFlushCache = false;

    /**
     * Global lifetime of the cache.
     */
    protected int $cacheMinutes = 60;

    /**
     * Set cache manager.
     */
    public static function setCacheInstance(CacheManager $cache)
    {
        self::$cache = $cache;
    }

    /**
     * Get cache manager.
     *
     * @return CacheManager
     */
    public static function getCacheInstance()
    {
        if (null === self::$cache) {
            self::$cache = app('cache');
        }

        return self::$cache;
    }

    /**
     * Determine if the cache will be skipped.
     *
     * @return bool
     */
    public function skippedCache()
    {
        return false === config('repositories.cache_enabled', false)
            || true === app('request')->has(config('repositories.cache_skip_param', 'skipCache'));
    }

    /**
     * Get Cache key for the method.
     *
     * @return string
     */
    public function getCacheKey(string $method, $args, string $tag)
    {
        // Sort through arguments
        foreach ($args as &$a) {
            if ($a instanceof Model) {
                $a = \get_class($a).'|'.$a->getKey();
            }
        }

        // Create hash from arguments and query
        $args = serialize($args).serialize($this->getScopeQuery());

        return sprintf(
            '%s-%s@%s-%s',
            config('app.locale'),
            $tag,
            $method,
            md5($args)
        );
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param mixed|null $time
     */
    public function cacheCallback(string $method, array $args, \Closure $callback, $time = null)
    {
        // Cache disabled, just execute query & return result
        if (true === $this->skippedCache()) {
            return \call_user_func($callback);
        }

        // Use the called class name as the tag
        $tag = static::class;

        return self::getCacheInstance()->tags(['repositories', $tag])->remember(
            $this->getCacheKey($method, $args, $tag),
            $this->getCacheExpiresTime($time),
            $callback
        );
    }

    /**
     * Flush the cache for the given repository.
     *
     * @return bool
     */
    public function flushCache()
    {
        // Cache disabled, just ignore this
        if (false === $this->eventFlushCache || false === config('repositories.cache_enabled', false)) {
            return false;
        }

        // Use the called class name as the tag
        $tag = static::class;

        return self::getCacheInstance()->tags(['repositories', $tag])->flush();
    }

    /**
     * Return the time until expires in minutes.
     *
     * @param int $time
     *
     * @return int
     */
    protected function getCacheExpiresTime($time = null)
    {
        if (self::EXPIRES_END_OF_DAY === $time) {
            return class_exists(Carbon::class)
                ? round(Carbon::now()->secondsUntilEndOfDay() / 60)
                : $this->cacheMinutes;
        }

        return $time ?: $this->cacheMinutes;
    }
}

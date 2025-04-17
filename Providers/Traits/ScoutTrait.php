<?php

declare(strict_types=1);

namespace Modules\Xot\Providers\Traits;

// use Modules\Xot\Engines\FullTextSearchEngine;

trait ScoutTrait
{
    private function registerScout(): void
    {
        // --- Scout lo ho tolto per ora
        /*
        resolve(\Laravel\Scout\EngineManager::class)->extend('fulltext', function () {
            return new FullTextSearchEngine();
        });
        */
    }
}

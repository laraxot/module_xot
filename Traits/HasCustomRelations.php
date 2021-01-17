<?php

namespace Modules\Xot\Traits;

/*
 * https://stackoverflow.com/questions/39213022/custom-laravel-relations
 * https://github.com/johnnyfreeman/laravel-custom-relation
 */

use Closure;
use Modules\Xot\Relations\CustomRelation;

//use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasCustomRelations
 * @package Modules\Xot\Traits
 */
trait HasCustomRelations {
    /**
     * @param $related
     * @param Closure $baseConstraints
     * @param Closure|null $eagerConstraints
     * @param Closure|null $eagerMatcher
     * @return CustomRelation
     */
    public function customRelation($related, Closure $baseConstraints, Closure $eagerConstraints = null, Closure $eagerMatcher = null) {
        $instance = new $related();
        $query = $instance->newQuery();

        return new CustomRelation($query, $this, $baseConstraints, $eagerConstraints, $eagerMatcher);
    }
}

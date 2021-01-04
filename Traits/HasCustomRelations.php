<?php

namespace Modules\Xot\Traits;

/*
 * https://stackoverflow.com/questions/39213022/custom-laravel-relations
 * https://github.com/johnnyfreeman/laravel-custom-relation
 */

use Closure;
use Modules\Xot\Relations\CustomRelation;

//use Illuminate\Database\Eloquent\Builder;

trait HasCustomRelations {
    public function customRelation($related, Closure $baseConstraints, Closure $eagerConstraints = null, Closure $eagerMatcher = null) {
        $instance = new $related();
        $query = $instance->newQuery();

        return new CustomRelation($query, $this, $baseConstraints, $eagerConstraints, $eagerMatcher);
    }
}

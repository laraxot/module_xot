<<<<<<< HEAD
<?php

declare(strict_types=1);

namespace Modules\Xot\Relations;

/*
 * https://github.com/johnnyfreeman/laravel-custom-relation/blob/master/src/Relations/Custom.php

 */

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class CustomRelation.
 */
class CustomRelation extends Relation {
    /**
     * The baseConstraints callback.
     */
    protected Closure $baseConstraints;

    /**
     * The eagerConstraints callback.
     *
     * @var \Closure
     */
    protected ?Closure $eagerConstraints;

    /**
     * The eager constraints model matcher.
     *
     * @var \Closure
     */
    protected ?Closure $eagerMatcher;

    /**
     * Create a new belongs to relationship instance.
     */
    public function __construct(Builder $query, Model $parent, Closure $baseConstraints, ?Closure $eagerConstraints, ?Closure $eagerMatcher) {
        $this->baseConstraints = $baseConstraints;
        $this->eagerConstraints = $eagerConstraints;
        $this->eagerMatcher = $eagerMatcher;

        parent::__construct($query, $parent);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        call_user_func($this->baseConstraints, $this);
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        //Parameter #1 $function of function call_user_func expects callable(): mixed, Closure|null given.
        call_user_func($this->eagerConstraints, $this, $models);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param array  $models
     * @param string $relation
     *
     * @return array
     */
    public function initRelation($models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param string $relation
     *
     * @return array
     */
    public function match(array $models, Collection $results, $relation) {
        //Trying to invoke Closure|null but it might not be a callable.
        return ($this->eagerMatcher)($models, $results, $relation, $this);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*']) {
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate out pivot
        // models with the result of those columns as a separate model relation.
        $columns = $this->query->getQuery()->columns ? [] : $columns;

        if ($columns == ['*']) {
            $columns = [$this->related->getTable().'.*'];
        }

        $builder = $this->query->applyScopes();

        $models = $builder->addSelect($columns)->getModels();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }
=======
<?php

declare(strict_types=1);

namespace Modules\Xot\Relations;

/*
 * https://github.com/johnnyfreeman/laravel-custom-relation/blob/master/src/Relations/Custom.php

 */

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class CustomRelation.
 */
class CustomRelation extends Relation {
    /**
     * The baseConstraints callback.
     */
    protected Closure $baseConstraints;

    /**
     * The eagerConstraints callback.
     *
     * @var \Closure
     */
    protected ?Closure $eagerConstraints;

    /**
     * The eager constraints model matcher.
     *
     * @var \Closure
     */
    protected ?Closure $eagerMatcher;

    /**
     * Create a new belongs to relationship instance.
     */
    public function __construct(Builder $query, Model $parent, Closure $baseConstraints, ?Closure $eagerConstraints, ?Closure $eagerMatcher) {
        $this->baseConstraints = $baseConstraints;
        $this->eagerConstraints = $eagerConstraints;
        $this->eagerMatcher = $eagerMatcher;

        parent::__construct($query, $parent);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        call_user_func($this->baseConstraints, $this);
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        //Parameter #1 $function of function call_user_func expects callable(): mixed, Closure|null given.
        call_user_func($this->eagerConstraints, $this, $models);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param array  $models
     * @param string $relation
     *
     * @return array
     */
    public function initRelation($models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param string $relation
     *
     * @return array
     */
    public function match(array $models, Collection $results, $relation) {
        //Trying to invoke Closure|null but it might not be a callable.
        return ($this->eagerMatcher)($models, $results, $relation, $this);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*']) {
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate out pivot
        // models with the result of those columns as a separate model relation.
        $columns = $this->query->getQuery()->columns ? [] : $columns;

        if ($columns == ['*']) {
            $columns = [$this->related->getTable().'.*'];
        }

        $builder = $this->query->applyScopes();

        $models = $builder->addSelect($columns)->getModels();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }
>>>>>>> 3c97c308c85924a62f31c89c71edfe23450749f0
}
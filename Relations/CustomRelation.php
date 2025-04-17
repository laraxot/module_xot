<?php
/**
 * ---.
 *
 * @see https://github.com/johnnyfreeman/laravel-custom-relation/blob/master/src/Relations/Custom.php
 */

declare(strict_types=1);

namespace Modules\Xot\Relations;

use function call_user_func;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class CustomRelation.
 *
 * @method static Builder when($value = null, callable $callback = null, callable $default = null)
 * @method static Builder whereBetween($column, iterable $values, $boolean = 'and', $not = false)
 * @method static Builder selectRaw($expression, array $bindings = []);
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class CustomRelation extends Relation
{ /* implements BuilderContract */
    /**
     * The baseConstraints callback.
     */
    protected \Closure $baseConstraints;

    /**
     * The eagerConstraints callback.
     *
     * @var \Closure
     */
    protected ?\Closure $eagerConstraints;

    /**
     * The eager constraints model matcher.
     *
     * @var \Closure
     */
    protected ?\Closure $eagerMatcher;

    /**
     * Create a new belongs to relationship instance.
     */
    public function __construct(Builder $query, Model $parent, \Closure $baseConstraints, ?\Closure $eagerConstraints, ?\Closure $eagerMatcher)
    {
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
    public function addConstraints()
    {
        \call_user_func($this->baseConstraints, $this);
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        // Parameter #1 $function of function call_user_func expects callable(): mixed, Closure|null given.
        if (! \is_callable($this->eagerConstraints)) {
            throw new \Exception('eagerConstraints is not callable');
        }
        \call_user_func($this->eagerConstraints, $this, $models);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param string $relation
     *
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
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
    public function match(array $models, Collection $results, $relation)
    {
        // Trying to invoke Closure|null but it might not be a callable.
        if (! \is_callable($this->eagerMatcher)) {
            throw new \Exception('eagerMatcher is not callable');
        }

        return ($this->eagerMatcher)($models, $results, $relation, $this);
    }

    /**
     * Get the results of the relationship.
     */
    public function getResults()
    {
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*'])
    {
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate out pivot
        // models with the result of those columns as a separate model relation.
        $columns = $this->query->getQuery()->columns ? [] : $columns;

        if ($columns === ['*']) {
            $columns = [$this->related->getTable().'.*'];
        }

        $builder = $this->query->applyScopes();

        $models = $builder->addSelect($columns)->getModels();

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (\count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }

    /*
     * Add a basic where clause to the query.
     *
     * @param \Closure|string|array|\Illuminate\Database\Query\Expression $column
     * @param mixed                                                       $operator
     * @param mixed                                                       $value
     * @param string                                                      $boolean
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function where($column, $operator = null, $value = null, $boolean = 'and') {
    //    return $this->query->where($column, $operator, $value, $boolean);
    // }
}

<?php
/**
 * https://laravelproject.com/laravel-filtering-query-using-pipelines/.
 * https://jeffochoa.me/understanding-laravel-pipelines.
 * https://dev.to/abrardev99/pipeline-pattern-in-laravel-278p.
 */
declare(strict_types=1);

namespace Modules\Xot\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Undocumented class.
 */
class Search
{
    /**
     * Undocumented function.
     *
     * @param Builder $query
     * @param array   ...$args
     *
     * @return \Closure
     */
    public function handle($query, \Closure $next, ...$args)
    {
        $search_fields = [];
        $model = $query->getModel();
        /**
         * @var string
         */
        $q = request('q', '');

        if (0 === \count($search_fields)) { // se non gli passo nulla, cerco in tutti i fillable
            $search_fields = $model->getFillable();
        }
        // $table = $model->getTable();
        if (\strlen($q) > 1) {
            $query = $query->where(
                function ($subquery) use ($search_fields, $q): void {
                    foreach ($search_fields as $k => $v) {
                        if (Str::contains($v, '.')) {
                            [$rel, $rel_field] = explode('.', $v);

                            // dddx([$rel, $rel_field]);
                            $subquery = $subquery->orWhereHas(
                                $rel,
                                function (Builder $subquery1) use ($rel_field, $q): void {
                                    // dddx($subquery1->getConnection()->getDatabaseName());

                                    $subquery1->where($rel_field, 'like', '%'.$q.'%');
                                    // dddx($subquery1);
                                }
                            );

                            // dddx($subquery);
                        } else {
                            $subquery = $subquery->orWhere($v, 'like', '%'.$q.'%');
                        }
                    }
                }
            );
        }
        // dddx(['q' => $q, 'sql' => $query->toSql()]);

        return $next($query);
    }
}

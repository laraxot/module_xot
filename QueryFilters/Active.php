<?php
/**
 * https://laravelproject.com/laravel-filtering-query-using-pipelines/.
 * https://jeffochoa.me/understanding-laravel-pipelines.
 * https://dev.to/abrardev99/pipeline-pattern-in-laravel-278p.
 * https://www.codechief.org/article/laravel-pipeline-interpretation-with-example.
 * https://medium.com/@jeffochoa/understanding-laravel-pipelines-a7191f75c351.
 */
declare(strict_types=1);

namespace Modules\Xot\QueryFilters;

use Illuminate\Support\Facades\Request;

class Active
{
    /**
     * Undocumented function.
     *
     * @param Request $request
     *
     * @return \Closure
     */
    public function handle($request, \Closure $next)
    {
        if (! request()->has('active')) {
            return $next($request);
        }

        return $next($request)->where('active', request()->input('active'));
    }
}

/*
 public function index(Request $request)
    {
     $posts = app(Pipeline::class)
            ->send(Post::query())
            ->through([
                \App\Filters\Active::class,
                \App\Filters\Sort::class
            ])
            ->thenReturn()
            ->get();

     return view('demo', compact('posts'));
    }


 */

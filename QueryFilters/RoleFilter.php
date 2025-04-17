<?php
/**
 * https://whysoawesome.com/posts/pipelines-in-laravel.
 */
declare(strict_types=1);

namespace Modules\Xot\QueryFilters;

use Closure;
use Illuminate\Support\Facades\Request;

/**
 * Undocumented class.
 */
class RoleFilter
{
    /**
     * we need to use laravel convention so we need to create the
     * method name 'handle' because when we use pipeline in laravel
     * then default method name is handle.
     *
     * request => argument that you passed in send() method
     * next => this closure will pass your argument to
     *                      next element in array that you have passed
     *                      in through() method
     *
     * @param Request $request
     *
     * @return mixed => you need to return your filtered data to next element
     */
    public function handle($request, \Closure $next)
    {
        if (! request()->has('role')) {
            return $next($request);
        }

        return $next($request)->where('role', request()->input('role'));
    }
}

/*
 public function create(Request $request)
{
    $pipes = [
        RemoveBadWords::class,
        ReplaceLinkTags::class,
        RemoveScriptTags::class
    ];

    $post = app(Pipeline::class)
    ->send($request)
    ->through($pipes)
    ->then(function ($content) {
        return Post::create(['content' => $content]);
    });

    //Return response
}

 */

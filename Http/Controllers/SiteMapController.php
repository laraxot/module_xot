<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Blog\Models\Post;

/**
 * Class SiteMapController.
 * https://kaloraat.com/articles/create-a-dynamic-xml-sitemap-in-laravel
 * https://laraget.com/blog/generate-a-simple-xml-sitemap-using-laravel.
 */
class SiteMapController {
    public function sitemap() {
        //return 'wip';

        //indicizzare solo i modelli traducibili?
        $posts = Post::groupBy('post_type')
            ->selectRaw('post_type')
            ->get();

        $posts = $posts->map(function ($item) {
            return $item->post_type;
        })->all();

        $posts = Arr::where($posts, function ($value) {
            if (null != $value) {
                return $value;
            }
            if (! Str::containsAll($value, ['Modules'])) {
                return $value;
            }
        });

        $items = collect();

        foreach ($posts as $post) {
            //$new_collection = xotModel($post)::with('post')->get();
            try {
                if (is_object(xotModel($post))) {
                    $new_collection = xotModel($post)::all()->map(function ($item) {
                        if ($item->post) {
                            return $item;
                        }
                    })->filter(function ($value) {
                        return ! is_null($value);
                    });
                    //qui non so se prenderli tutti oppure una parte, ho letto che il sitemap non può essere più grande di tot mega
                }
            } catch (\Throwable $th) {
                dddx($posts);
            }

            $items = $items->merge($new_collection);
        }

        //dddx($items);

        return response()->view('xot::sitemap', [
            'items' => $items,
        ])->header('Content-Type', 'text/xml');
    }
}

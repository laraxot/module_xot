<?php

namespace Modules\Xot\Tests\Feature;

/*
* https://devdojo.com/tutorials/simple-laravel-route-testing
*
**/

use Modules\Blog\Models\Post;
use Modules\Xot\Services\PanelService as Panel;
use Tests\TestCase;

//use Tests\BrowserKitTestCase as TestCase;

class RouteTest extends TestCase {
    /**
     * A basic test example.
     */
    public function myTestUrl($url) {
        $response = $this->get($url); //homepage
        echo PHP_EOL.'url: '.$url.'  status: '.$response->status();
        /*
        if (200 == (int) $response->status()) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
        //echo  PHP_EOL;
        */
        $response->assertStatus(200);
    }

    public function testRoutes() {
        $langs = config('laravellocalization.supportedLocales');
        $roots = config('xra.roots');
        //-------- HOME -------------
        $url = '/';
        $this->myTestUrl($url);

        foreach ($langs as $lang => $lang_arr) {
            \App::getLocale($lang);
            $url = '/'.$lang;
            $this->myTestUrl($url);

            foreach ($roots as $root) {
                $parz = ['lang' => $lang, 'container0' => $root];
                $url = route('container0.index', $parz);
                $this->myTestUrl($url);

                if ($populate = 0) {
                    $model = xotModel($root);
                    $rows = factory(get_class($model), 5)->create();
                    foreach ($rows as $k => $row) {
                        $post = $row->post()->save(factory(Post::class)->create(['lang' => $lang]));
                        $rows[$k]->post = $post;
                    }
                }

                $model = xotModel($root);
                $rows = $model->limit(5)->get();
                foreach ($rows as $k => $row) {
                    $panel = Panel::get($row);
                    $url = $panel->url();
                    echo PHP_EOL.' post_id: '.$row->post_id.'  post_type: '.$row->post_type.'  lang: '.$row->lang;
                    $this->myTestUrl($url);
                }
            }
        }
    }

    //end function
}//end class

<?php

namespace Modules\Xot\Tests\Feature;

/*
* https://devdojo.com/tutorials/simple-laravel-route-testing
*
**/

use Tests\TestCase;


use Modules\Blog\Models\Post;
use Modules\Food\Models\Restaurant;

class RouteTest extends TestCase {
    /**
     * A basic test example.
     */
    public function testRoutes() {

        $urls = [
            '/',
            '/url2',
            '/url3',
        ];

        $langs=config('laravellocalization.supportedLocales');

        $roots=config('xra.roots');


        echo  PHP_EOL;
        $url='/';
        $response = $this->get($url); //homepage
        echo chr(13).''.$url.'  '.$response->status();
        if((int)$response->status() == 200){
            $this->assertTrue(true);
        }else{
            $this->assertTrue(false);
        }
        echo  PHP_EOL;
        foreach($langs as $lang=>$lang_arr){
            $url='/'.$lang;
            $response = $this->get($url); //homepage
            echo chr(13).''.$url.'  '.$response->status();
            if((int)$response->status() == 200){
                //$this->assertTrue(true);
            }else{
                //$this->assertTrue(false);
            }
            echo  PHP_EOL;
            foreach($roots as $root){

                //$url='/'.$lang.'/'.$root;
                $parz=[
                    'lang'=>$lang,
                    'container0'=>$root,
                ];
                //container0.index
                $url=route('container0.index',$parz);
                $response = $this->get($url);
                echo chr(13).''.$url.'  '.$response->status();
                $model=xotModel($root);
                $risto=factory(Restaurant::class)->make();
                dd($risto);
                /*
                dd($risto);


                $rows=factory(get_class($model),5)
                    ->create()
                    ->each(function ($row){
                        dd(1);
                        $post=factory(Post::class)->make();
                        $row->post()->save($post);
                    });
                dd($rows);

                //$rows=$model->all();
                foreach($rows as $row){
                    echo chr(13).$row->title;
                }
                    */

                //$response->assertStatus(200);
               
                //$this->assertResponseOk();
                echo  PHP_EOL;
            }
        }

        $this->assertTrue(true);

        //$this->call('GET',$url);
        //$this->assertResponseOk();


        /*
        foreach ($urls as $url) {
            $response = $this->get($url);
            if (200 !== (int) $response->status()) {
                echo  $appURL.$url.' (FAILED) did not return a 200.';
                $this->assertTrue(false);
            } else {
                echo $appURL.$url.' (success ?)';
                $this->assertTrue(true);
            }
            echo  PHP_EOL;
        }
        */
    }
}

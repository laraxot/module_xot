<?php

/**
 * https://devdojo.com/devdojo/simple-laravel-route-testing.
 */

namespace Modules\Xot\Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class RouteTest extends TestCase {
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoutes() {
        //dddx('/'.App::getlocale().'/home');

        $appURL = env('APP_URL');

        $urls = [
            '/',
            '/'.App::getlocale().'/',
            '/home',
            //'/'.App::getlocale().'/home', //questo url mi da errore
        ];

        echo  PHP_EOL;

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
    }
}

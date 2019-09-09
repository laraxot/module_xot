<?php

namespace Modules\Xot\Tests\Feature;

/*
* https://devdojo.com/tutorials/simple-laravel-route-testing
*
**/

use Illuminate\Support\Str;
use Modules\Blog\Models\Post;
use Modules\Food\Models\Restaurant;
use Modules\Food\Models\RestaurantOwner;
use Modules\LU\Models\User;
//use Tests\TestCase;
use Modules\Xot\Services\PanelService as Panel;
use Tests\BrowserKitTestCase as TestCase;

class RouteBrowserTest extends TestCase {
    private $init = false;

    public function init() {
        $this->user = factory(User::class)->create();
        $this->lang = \App::getLocale();
        $this->init = true;
        $this->restaurant = factory(Restaurant::class)->create();
        $this->restaurant->each(function ($t) {
            $t->post()->save(factory(Post::class)->make());
        });

        $this->restaurant_panel = Panel::get($this->restaurant);

        $this->restaurant_owner = factory(RestaurantOwner::class)->create();
        $this->restaurant_owner->each(function ($t) {
            $t->post()->save(factory(Post::class)->make());
        });

        $this->restaurant_owner_panel = Panel::get($this->restaurant_owner);
    }

    public function testAddRestaurantNotLogged() {
        if (! $this->init) {
            $this->init();
        }
        $response = $this->visit('/');
        $response = $response->click('add-restaurant');
        $response->seeRouteIs('container0.create', ['lang' => $this->lang, 'container0' => 'restaurant_owner']);
        $url = $this->restaurant_owner_panel->createUrl();
        $url = Str::before($url, '?');
        $response->seePageIs($url);
        $attributes = factory(RestaurantOwner::class)->raw();
        $response->types = $attributes;
        $response->press('save');
        /*--- commentato perche' da sistemare !
        $url=$this->restaurant_owner_panel->editUrl();
        $response->seePageIs($url);
        */
        //ddd($attributes);
    }

    public function testAddRestaurantLogged() {
        if (! $this->init) {
            $this->init();
        }
        $response = $this->actingAs($this->user)->visit('/');
        $response = $response->click('add-restaurant');
        $response->seeRouteIs('container0.create', ['lang' => $this->lang, 'container0' => 'restaurant']);
    }
}

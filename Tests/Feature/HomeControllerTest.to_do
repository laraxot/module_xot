<?php

namespace Modules\Xot\Tests\Feature;

/*
 * https://jasonmccreary.me/articles/start-testing-laravel/
 * https://laravel-news.com/testing-laravel-middleware
 * https://dev.to/edmilsonrobson/laravel-55-testing-and-you-8hl
 * https://mauricius.dev/testing-approaches-for-laravel-form-requests/
 * https://whoisryosuke.com/blog/2018/adding-testing-to-laravel-api/


 */

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\LU\Models\User;
use Tests\TestCase;

class HomeControllerTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function index_returns_a_view() {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }
}

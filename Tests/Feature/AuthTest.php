<?php

namespace Modules\Xot\Tests\Feature;

/*
 * https://jasonmccreary.me/articles/start-testing-laravel/
 * https://www.5balloons.info/laravel-tdd-beginner-crud-example/
 * https://tighten.co/blog/tidying-up-your-phpunit-tests-with-data-providers/
 * http://laravel-italia.it/articoli/testing-dei-model-like-a-boss-parte-1
 * https://m.dotdev.co/test-driven-api-development-using-laravel-dingo-and-jwt-with-documentation-ae4014260148
 * https://github.com/laravelio/laravel.io
 * https://laravelpackage.com/13-jobs.html#creating-a-job

 */

use Tests\TestCase;

class AuthTest extends TestCase {
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample() {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testLogiDdisplaysTheLoginForm() {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        //$response->assertViewIs('auth.login'); //adm_theme::auth.login
    }

    /** @test */
    public function testLoginDisplaysValidationErrors() {
        $url = route('login');
        $response = $this->post($url, []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    /*-- mi interessa creare solo da post
    public function login_authenticates_and_redirects_user() {
        $user = factory(\Modules\LU\Models\User::class)->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        //$response->assertRedirect(route('home')); //Route [home] not defined
        $this->assertAuthenticatedAs($user);
    }
    */

    /** @test */
    public function register_creates_and_authenticates_a_user() {
        $response = $this->post('register', [
            'name' => 'JMac',
            'email' => 'jmac@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));

        // assertDatabaseHas(string $table, array $data, string $connection = null)

        $this->assertDatabaseHas('users', [
            'name' => 'JMac',
            'email' => 'jmac@example.com',
        ]);

        $user = User::where('email', $email)->where('name', $name)->first();
        $this->assertNotNull($user);

        $this->assertAuthenticatedAs($user);
    }
}

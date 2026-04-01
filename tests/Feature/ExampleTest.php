<?php

namespace Tests\Feature;

use App\Setting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        Setting::set('site_name', 'PTA Test');
        Setting::set('address', 'Alamat Test');
        Setting::set('email', 'test@example.com');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PTA Test');
        $response->assertSee('Alamat Test');
        $response->assertSee('test@example.com');
    }
}

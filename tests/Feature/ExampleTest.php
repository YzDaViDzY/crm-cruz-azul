<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_guest_is_redirected_to_login()
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_suite_runs_on_sqlite_in_memory_only()
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertFalse(config('app.seed_demo_data'));
    }

    public function test_demo_seeder_is_blocked_without_explicit_flag()
    {
        $this->expectException(\RuntimeException::class);

        (new \Database\Seeders\DatabaseSeeder())->run();
    }

    public function test_demo_seeder_never_overwrites_existing_users()
    {
        config(['app.seed_demo_data' => true]);
        \App\Models\User::create([
            'name' => 'Existente',
            'email' => 'admin@cruzazul.com',
            'password' => \Illuminate\Support\Facades\Hash::make('clave-original'),
            'role' => 'arrendatario',
        ]);

        (new \Database\Seeders\DatabaseSeeder())->run();

        $user = \App\Models\User::where('email', 'admin@cruzazul.com')->first();
        $this->assertSame('Existente', $user->name);
        $this->assertSame('arrendatario', $user->role);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('clave-original', $user->password));
    }

    public function test_admin_dashboard_requires_authentication()
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }
}

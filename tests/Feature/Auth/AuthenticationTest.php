<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen()
    {

        $response = $this->post('/api/v1/auth/login', [
            'email' => "admin@foodics.com",
            'password' => 'P@55w0rd@Foodics',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(201);
        $this->assertEquals(true, $response['success']);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }


}

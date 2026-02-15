<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class RadiusAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test PPPoE authentication.
     *
     * @return void
     */
    public function test_pppoe_authentication()
    {
        // Mock the User model
        $user = new User([
            'username' => 'testuser',
            'password' => Hash::make('password'),
        ]);

        $userMock = Mockery::mock('alias:App\Models\User');
        $userMock->shouldReceive('where')->with('username', 'testuser')->andReturnSelf();
        $userMock->shouldReceive('first')->andReturn($user);

        // Send a request to the fake RADIUS endpoint
        $response = $this->postJson('/api/fake-radius/authenticate', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        // Assert that the response is Access-Accept
        $response->assertStatus(200);
        $response->assertJson([
            'Response-Packet' => 'Access-Accept',
        ]);
    }
}

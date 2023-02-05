<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    public function testRequiredFieldsForLogin()
    {
        $this->json('POST', 'api/login', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            "message" =>  [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ]
        ]);
    }

    // public function testSuccessfulLogin()
    // {
        // $this->artisan('passport:client', ['--no-interaction' => true]);

        // $user = User::create([
        //    'email' => 'test@test.com',
        //    'password' => md5('Test@123'),
        //    'name' => 'Test', 
        //    'is_admin' => 0,
        // ]);

        // $loginData = ['email' => 'test@test.com', 'password' => 'Test@123'];

        // $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
        //     ->assertStatus(200);
        //     // ->assertJson([
        //     //     'code' => '200',
        //     //     'message' => 'Successfully Login',
        //     // ]);

        // $this->assertAuthenticated();
    // }
}

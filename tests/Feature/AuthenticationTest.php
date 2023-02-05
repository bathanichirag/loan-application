<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void {
        parent::setUp();
        \Artisan::call('migrate',['-vvv' => true]);
        \Artisan::call('passport:install',['-vvv' => true]);
        \Artisan::call('db:seed',['-vvv' => true]);
    }

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

    public function testSuccessfulLogin()
    {       
        $user = User::create([
           'email' => 'test@test.com',
           'password' => md5('Test@123'),
           'name' => 'Test', 
           'is_admin' => 0,
        ]);

        $user->createToken('LOANAPP')->accessToken; 

        $loginData = ['email' => 'test@test.com', 'password' => 'Test@123'];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    "access_token",
                    "token_type",
                    "expires_at",
                ]
            ]);
    }

}

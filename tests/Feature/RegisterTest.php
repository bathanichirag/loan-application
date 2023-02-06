<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Function to check validation for register.
     * @return void
     */
    public function testRequiredFieldsForRegister()
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            "message" =>  [
                "name" => ["The name field is required."],
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ]
        ]);
    }

    /**
     * Function to check validation for register feature.
     * @return void
     */
    public function testSuccessfulRegister()
    {
        $postData = ['name' => 'Test', 'email' => 'test@test.com', 'password' => 'Test@123'];

        $this->json('POST', 'api/register', $postData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                'code' => '201',
                'message' => 'User successfully registered',
            ]);
    }





}

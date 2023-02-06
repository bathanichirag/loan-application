<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\PassportTestCase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class LogoutTest extends PassportTestCase
{
    use DatabaseTransactions;

    /**
     * Function to check validation for logout.
     * @return void
     */
    public function testSuccessfulLogout()
    {
        $this->getJson('api/logout')
            ->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data'
            ]);
    }
}

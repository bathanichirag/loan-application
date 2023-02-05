<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\PassportTestCase;

class LoanTest extends PassportTestCase
{
    public function testRequiredFieldsForApplyLoan()
    {
        $this->postJson('api/apply-loan', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => [
                    "amount" => ["The amount field is required."],
                    "terms" => ["The terms field is required."]
                ]
            ]);
    }

}

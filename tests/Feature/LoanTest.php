<?php

namespace Tests\Feature;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\Feature\PassportTestCase;

class LoanTest extends PassportTestCase
{
    /**
     * Function to check validation for Apply Loan.
     * @return void
     */
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

    /**
     * Function to check apply loan feature.
     * @return void
     */
    public function testSuccessfulApplyLoan()
    {
        $user_id = $this->user->id;
        $term_amount = 15000 / 4;
        $postData = ['amount' => 15000, 'terms' => 4, 'user_id' => $user_id, 'remaining_amount' => 15000, 'status' => 'Pending', 'paid_terms' => 0, 'term_amount' => $term_amount];

        Loan::create($postData);
        $this->postJson('api/apply-loan', $postData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    "amount",
                    "terms",
                    "user_id",
                    "remaining_amount",
                    "term_amount",
                    "status",
                    "updated_at",
                    "created_at",
                    "id"
                ]
            ]);
    }

    /**
     * Function to check validation for change loan status.
     * @return void
     */
    public function testRequiredFieldsForChangeLoanStatus()
    {
        $this->postJson('api/change-loan-status', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => [
                    "status" => ["The status field is required."],
                    "loan_id" => ["The loan id field is required."]
                ]
            ]);
    }

    /**
     * Function to check validation change loan status feature.
     * @return void
     */
    public function testSuccessfulChangeLoanStatus()
    {
        $user_id = $this->user->id;
        $term_amount = 15000 / 4;
        $postData = ['amount' => 15000, 'terms' => 4, 'user_id' => $user_id, 'remaining_amount' => 15000, 'status' => 'Pending', 'paid_terms' => 0, 'term_amount' => $term_amount];

        $loan = Loan::create($postData);

        $loan->update(['status'=> 'Approved']);


        $postDataForApprove = ['loan_id' => $loan->id, 'status' => 'Approved'];

        $this->postJson('api/change-loan-status', $postDataForApprove, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    "amount",
                    "terms",
                    "user_id",
                    "remaining_amount",
                    "term_amount",
                    "status",
                    "updated_at",
                    "created_at",
                    "id"
                ]
            ]);
    }

    /**
     * Function to check validation for get loan list.
     * @return void
     */
    public function testRequiredFieldsForGetLoanList()
    {
        $this->getJson('api/get-loans', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => [
                    "limit" => ["The limit field is required."],
                    "offset" => ["The offset field is required."]
                ]
            ]);
    }

    /**
     * Function to check validation for get loan list.
     * @return void
     */
    public function testSuccessfulGetLoanList()
    {

        $getData = ["limit" => 10, "offset" => 0];
        $this->get('api/get-loans?limit=10&offset=0')
            ->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    '*' => [
                        "amount",
                        "terms",
                        "remaining_amount",
                        "term_amount",
                        "status",
                        "paid_terms",
                        "start_date",
                        "updated_at",
                        "created_at",
                        "id"
                    ]
                ]
            ]);
    }


}

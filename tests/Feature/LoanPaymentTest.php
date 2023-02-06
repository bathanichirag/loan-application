<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\Feature\PassportTestCase;

class LoanPaymentTest extends PassportTestCase
{

    /**
     * Function to check validation for pay loan.
     * @return void
     */
    public function testRequiredFieldsPayLoan()
    {
        $this->postJson('api/loan-payment', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => [
                    "amount" => ["The amount field is required."],
                    "loan_id" => ["The loan id field is required."]
                ]
            ]);
    }

    /**
     * Function to check pay loan feature.
     * @return void
     */
    public function testSuccessfulPayLoan()
    {
        $user_id = $this->user->id;
        $term_amount = 15000 / 4;
        $postData = ['amount' => 15000, 'terms' => 4, 'user_id' => $user_id, 'remaining_amount' => 15000, 'status' => 'Pending', 'paid_terms' => 0, 'term_amount' => $term_amount];

        $loan = Loan::create($postData);

        DB::table('loans')->where('id', $loan->id)->update(array('status' => 'Approved'));

        $postDataForLoanPay = ['loan_id' => $loan->id, 'amount' => $term_amount];

        $this->postJson('api/loan-payment', $postDataForLoanPay, ['Accept' => 'application/json'])
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
                    "start_date",
                    "paid_terms",
                    "status",
                    "updated_at",
                    "created_at",
                    "id"
                ]
            ]);
    }

    /**
     * Function to check validation for payment detail list.
     * @return void
     */
    public function testRequiredFieldsForGetPaymentDetails()
    {
        $this->getJson('api/get-loan-payments', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => [
                    "limit" => ["The limit field is required."],
                    "offset" => ["The offset field is required."],
                    "loan_id" => ["The loan id field is required."]
                ]
            ]);
    }

    /**
     * Function to check get payment detail feature.
     * @return void
     */
    public function testSuccessfulGetPaymentDetails()
    {

        $user_id = $this->user->id;
        $term_amount = 15000 / 4;
        $postData = ['amount' => 15000, 'terms' => 4, 'user_id' => $user_id, 'remaining_amount' => 15000, 'status' => 'Pending', 'paid_terms' => 0, 'term_amount' => $term_amount];

        $loan = Loan::create($postData);

        DB::table('loans')->where('id', $loan->id)->update(array('status' => 'Approved'));

        $postDataForLoanPay = ['loan_id' => $loan->id, 'amount' => $term_amount];

        $loanPayment = LoanPayment::create($postDataForLoanPay);

        $this->get('api/get-loan-payments?limit=10&offset=0&loan_id='.$loan->id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    '*' => [
                        "id",
                        "loan_id",
                        "amount",
                        "updated_at",
                        "created_at"
                    ]
                ]
            ]);
    }



}

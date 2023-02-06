<?php

namespace App\Http\Controllers\LoanPayment;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanPaymentController extends Controller
{

    /**
     * Loan Payments API to repay loan amounts.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function payLoan(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|decimal:0,2|min:1|max:99999999',
                'loan_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }

            $user_id = $request->user()->id;

            $loan = Loan::where(['id' => $request->loan_id, 'user_id' => $user_id])->first();

            if (empty($loan)) {
                return ResponseHelper::notFoundMessage('No Loan Found', 404);
            }

            $status = ($loan->status) ? $loan->status : '';
            $remaining_amount = ($loan->remaining_amount) ? $loan->remaining_amount : 0;
            $term_amount = ($loan->term_amount) ? $loan->term_amount : 0;
            $terms = ($loan->terms) ? $loan->terms : 0;
            $paid_terms = ($loan->paid_terms) ? $loan->paid_terms : 0;
            $pending_terms = $terms - $paid_terms;

            if($status === 'Approved') {
                if($request->amount > $remaining_amount) {
                    return ResponseHelper::notFoundMessage('Amount is more then remaining amount. Remaining amount is '.$remaining_amount, 422);
                }

                if(($pending_terms !== 1 && (($request->amount >= $term_amount) || ($request->amount == $remaining_amount)))
                    || ($pending_terms == 1 && ($request->amount == $remaining_amount))) {

                    $updated_remaining_amount = $remaining_amount - $request->amount;
                    $update_status = $status;

                    if($updated_remaining_amount <= 0) {
                        $update_status = 'Paid';
                    }

                    $loanPayments = LoanPayment::create($validator->validated());

                    $loan->status = $update_status;
                    $loan->remaining_amount = $updated_remaining_amount;
                    $loan->paid_terms = $paid_terms + 1;

                    $loan->save();

                    return ResponseHelper::successfulMessage(201, 'Loan Payment successfully paid.', '', $loan);

                } else {
                    if($pending_terms !== 1) {
                        return ResponseHelper::notFoundMessage('Amount should be >= '.$term_amount.' OR = '.$remaining_amount , 422);
                    } else {
                        return ResponseHelper::notFoundMessage('Amount should be '.$remaining_amount , 422);
                    }
                }

            } else if($status === 'Paid') {
                return ResponseHelper::successfulMessage(200, 'Your loan is already Paid.', '', '');
            } else {
                return ResponseHelper::notFoundMessage('Your Loan status is '.$status, 422);
            }
        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 422);
        }
    }

    /**
     * Get payment details by loan id. For both admin and customers.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function getPaymentDetails(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'limit' => 'required|integer|min:1|max:100',
                'offset' => 'required|integer',
                'loan_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }

            $is_admin = $request->user()->is_admin;
            $user_id = $request->user()->id;

            $loan = Loan::where('id', $request->loan_id);
            if(!$is_admin) {
                $loan->where('user_id', $user_id);
            }
            $loan_result = $loan->first();

            if(empty($loan_result)) {
                return ResponseHelper::notFoundMessage('No loan found.', 404);
            }

            $total_count = 0;
            $loan_payments = LoanPayment::where('loan_id', $request->loan_id);

            $total_count = $loan_payments->get()->count();

            $loan_payments->orderBy('id', 'DESC');
            $loan_payments->take($request->limit);
            $loan_payments->skip($request->offset);
            $result = $loan_payments->get();

            return ResponseHelper::successfulMessage(200, 'Loans payments List', $total_count, $result);

        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 422);
        }
    }
}

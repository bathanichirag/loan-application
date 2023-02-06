<?php

namespace App\Http\Controllers\Loan;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{

    /**
     * Apply Loan for customers.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function applyForLoan(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'amount' => 'required|decimal:0,2|min:1|max:99999999',
                'terms' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }

            $user_id = $request->user()->id;
            $remaning_amount = $request->amount;
            $term_amount = ($request->amount / $request->terms);

            $loan = Loan::create(array_merge(
                $validator->validated(),
                ['user_id' => $user_id, 'remaining_amount' => $remaning_amount, 'term_amount'=> $term_amount, 'status' => 'Pending', 'paid_terms' => 0]
            ));

            return ResponseHelper::successfulMessage(201, 'Loan request submitted successfully', '', $loan);
        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 404);
        }
    }

    /**
     * Chnage loan status from pending to Approved or Reject
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function changeLoanStatus(Request $request) {
        try {
            $is_admin =  $request->user()->is_admin;

            if(!$is_admin) {
                return ResponseHelper::notFoundMessage("Access denied", 403);
            }

            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|integer|min:1',
                'status' => 'required|string|in:Approved,Reject'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }

            $loan = Loan::where(['id' => $request->loan_id])->first();

            if (empty($loan)) {
                return ResponseHelper::notFoundMessage('No Loan Found', 404);
            }

            $loan->status = $request->status;
            $loan->start_date = date('Y-m-d');

            $loan->save();

            return ResponseHelper::successfulMessage(200, 'Loan status changed', '', $loan);
        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 404);
        }
    }

    /**
     * To get loan list for admin and customers with filters and pagination.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function getLoansList(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'limit' => 'required|integer|min:1|max:100',
                'offset' => 'required|integer',
                'status' => 'nullable|in:Approved,Reject,Cancel,Paid,Pending',
                'customer_id' => 'nullable|integer',
                'loan_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }

            $is_admin = $request->user()->is_admin;
            $user_id = $request->user()->id;

            if(!$is_admin) {
                $request->customer_id = $user_id;
            }
            $total_count = 0;
            $loans = Loan::select('id','amount','terms','start_date','remaining_amount','term_amount','paid_terms','status','created_at','updated_at');

            if($request->customer_id) {
                $loans->where('user_id', $request->customer_id);
            }

            if($request->status) {
                $loans->where('status', $request->status);
            }

            if($request->loan_id) {
                $loans->where('id', $request->loan_id);
            }

            $total_count = $loans->get()->count();

            $loans->orderBy('id', 'DESC');
            $loans->take($request->limit);
            $loans->skip($request->offset);
            $result = $loans->get();

            return ResponseHelper::successfulMessage(200, 'Loans List', $total_count, $result);

        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 422);
        }
    }
}

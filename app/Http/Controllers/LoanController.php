<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{

    public function applyForLoan(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'amount' => 'required|decimal|min:1|max:99999999',
                'terms' => 'required|integer|min:1'
            ]);

            $user_id = $request->user()->id;
            //$remaning_amount =

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => md5($request->password), 'is_admin' => 0]
            ));

            return ResponseHelper::successfulMessage(201, 'User successfully registered', '', $user);
        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 404);
        }
    }
}

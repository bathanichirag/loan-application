<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;

class AuthController extends Controller
{

    /**
     * Login API for Customer and Admin users.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::notFoundMessage($validator->errors(), 422);
            }

            $user = User::where(['email' => $request->email, 'password' => md5($request->password)])->first();

            if (empty($user)) {
                return ResponseHelper::notFoundMessage('Unauthorized', 401);
            }

            $tokenResult = $user->createToken('LOANAPP');
            $token = $tokenResult->token;

            $token->save();

            $responseData = [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => date('Y-m-d H:i:s', strtotime($tokenResult->token->expires_at))
            ];

            return ResponseHelper::successfulMessage(200, 'Successfully Login', '', $responseData);
        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 404);
        }

    }

    /**
     * Logout user api.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return ResponseHelper::successfulMessage(200, 'Successfully logged out', '', '');
        } catch (\Exception $ex) {
            return ResponseHelper::notFoundMessage($ex->getMessage(), 404);
        }
    }

    /**
     * Register customer users.
     * @param Request $request
     * @return \App\Helpers\type
     */
    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:6',

            ]);

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

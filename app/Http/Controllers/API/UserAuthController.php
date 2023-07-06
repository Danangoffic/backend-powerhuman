<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserAuthController extends Controller
{
    private static $AUTH_FAILED = 'Authentication Failed';

    /**
     * Login User
     * @param Request $request
     * @return ResponseFormatter $response
     */
    public function login(Request $request)
    {
        try {
            // validate request user
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ]);
            // find user by email
            $user = User::where('email', $request->email)->first();
            // check user
            if (!$user || !Hash::check($request->password, $user->password)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], $this->AUTH_FAILED, 401);
            }
            // attempting user login
            if (!Auth::attempt($request->only('email', 'password'), $request->remember_me)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], $this->AUTH_FAILED, 401);
            }
            // create token
            $token = $user->createToken('authToken')->plainTextToken;
            // extending session if user checked stay logged in for user session token
            // $cookie = cookie('jwt', $token, 60 * 24 * 7);
            // response
            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                // 'cookie' => $cookie,
                // 'validated_session' => '7 days'
            ], 'Authenticated');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * Register User
     * @param Request $request
     * @return ResponseFormatter $response
     */
    public function register(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => ['required', 'string', new Password(8)]
            ]);
            // create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            // create token
            $token = $user->createToken('authToken')->plainTextToken;
            // response
            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * Logout User
     * @param Request $request
     * @return ResponseFormatter $response
     */
    public function logout(Request $request)
    {
        try {
            // revoke all token
            // $request->user()->tokens()->delete();
            // revoke user token
            $request->user()->currentAccessToken()->delete();
            // revoke specific token
            // $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();
            // response
            return ResponseFormatter::success([], 'Token Revoked');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 'Logout Failed', 500);
        }
    }

    /**
     * Fetch User
     * @param Request $request
     * @return ResponseFormatter $response
     */
    public function fetch_user(Request $request)
    {
        try {
            // get user
            $user = $request->user();
            // response
            return ResponseFormatter::success($user, 'Fetch Success');
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 'Fetch User Failed', 500);
        }
    }
}

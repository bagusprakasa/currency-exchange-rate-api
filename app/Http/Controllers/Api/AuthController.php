<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // validate request and get validated request
        $validated = $request->validated();

        try {
            // insert new user
            $newUser = new User;
            $newUser->name = $validated['name'];
            $newUser->email = $validated['email'];
            $newUser->password = bcrypt($validated['password']);
            $newUser->save();

            // create user access token
            $token = $newUser->createToken('auth-token')->accessToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Register successfully.'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        } catch (QueryException $error) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        // validate request and get validated request
        $validated = $request->validated();

        $data = [
            'email' => $validated['email'],
            'password' => $validated['password']
        ];

        // login attempt
        if (auth()->attempt($data)) {
            // create user access token for authenticated user
            $token = auth()->user()->createToken('auth-token')->accessToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login successfully.',
                'data' => [
                    'token' => $token,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
    }
}

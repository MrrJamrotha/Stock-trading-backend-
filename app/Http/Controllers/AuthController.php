<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected Service $service;
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return $this->service->validate($validator);
            }

            $credentials = $request->only('email', 'password');
            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            return $this->service->errorMessage($exception);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->service->validate($validator);
            }
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Register successfully',
            ]);
        } catch (\Exception $exception) {
            return $this->service->errorMessage($exception);
        }
    }

    // User logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['status' => 'success', 'message' => 'Successfully logged out'], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        try {
            if (! auth()->check()) {
                return $this->service->unauthorizedMessage();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Get profile information successfully',
                'record' => auth()->user(),
            ]);
        } catch (\Exception $exception) {
            return $this->service->errorMessage($exception);
        }
    }

    public function refresh()
    {
        try {
            return $this->respondWithToken(auth()->refresh());
        } catch (\Exception $exception) {
            return $this->service->errorMessage($exception);
        }
    }
}

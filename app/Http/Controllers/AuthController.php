<?php

namespace App\Http\Controllers;

use App\Service\Service;
use Illuminate\Http\Request;
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
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            $user = auth()->user();
            $token = auth()->claims(['role' => $user->role])->fromUser($user);

            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            return $this->service->errorMessage();
        }
    }

    public function register(Request $request)
    {
        try {
        } catch (\Exception $exception) {
            return $this->service->errorMessage();
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
        return response()->json(auth()->user());
    }
}

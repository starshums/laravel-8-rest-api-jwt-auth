<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\Controller;

class AuthController extends Controller {
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request) {
        $validator = Validator::make(
            $request->all(), [
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $token_validity = (24 * 60);

        auth()->factory()->setTTL($token_validity);

        if (!$token = auth()->attempt($validator->validated())) {
            throw new AuthenticationException();
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make(
            $request->all(), [
                'name'     => 'required|string|between:2,100',
                'email'    => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User logged out successfully']);
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token) {
        return response()->json([
                'token'          => $token,
                'token_type'     => 'bearer',
                'token_validity' => (auth()->factory()->getTTL() * 60),
            ]
        );
    }
}

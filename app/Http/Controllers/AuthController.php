<?php

namespace App\Http\Controllers;

use App\Exceptions\UnauthorizedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'data' => $user,
            'credentials' => [
                'token' => $token
            ]
        ], 201);
    }

    public function login(LoginRequest $request)
{
    $data = $request->validated();

    if (!auth()->attempt($data)) {
        throw new UnauthorizedException();
    }

    $user = auth()->user();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        "message" => "success",
        "data" => [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email
        ],
        "credentials" => [
            "token" => $token,
        ]
    ]);
}

public function logout()
{
    auth()->user()->tokens()->delete();
    return response()->noContent();
}
}

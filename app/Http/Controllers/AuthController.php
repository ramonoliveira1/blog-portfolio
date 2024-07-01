<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password', 'device_name');

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $passwordRight = Hash::check($credentials['password'], $user->password);

        if (!$user || !$passwordRight) {
            return response()->json(['message' => 'Email or password incorrect.'], 401);
        }

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->only('name', 'email', 'password');

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json($user);
    }
}

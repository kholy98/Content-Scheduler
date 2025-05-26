<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken
        ]);
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        return response()->json([
            'token' => auth()->user()->createToken('api-token')->plainTextToken
        ]);
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

}
